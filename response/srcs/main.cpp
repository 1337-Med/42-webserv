#include <sys/socket.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <iostream>
#include <cstring>
#include <sstream>
#include <fstream>
#include "response.hpp"

#define PORT 8080
#define BUFFER_SIZE 1024
void handleClient(int clientSocket) {
    std::string fullRequest;
    char buffer[BUFFER_SIZE];
    ssize_t bytesRead;
    bool headersComplete = false;
    size_t contentLength = 0;
    size_t headerEndPos = 0;

    while ((bytesRead = recv(clientSocket, buffer, BUFFER_SIZE - 1, 0)) > 0) {
        buffer[bytesRead] = '\0';
        fullRequest.append(buffer, bytesRead);

        if (!headersComplete) {
            headerEndPos = fullRequest.find("\r\n\r\n");
            if (headerEndPos != std::string::npos) {
                headersComplete = true;
                size_t clPos = fullRequest.find("Content-Length: ");
                if (clPos != std::string::npos) {
                    clPos += 16;
                    size_t clEnd = fullRequest.find("\r\n", clPos);
                    contentLength = std::atoi(fullRequest.substr(clPos, clEnd - clPos).c_str());
                }
            }
        }

        if (headersComplete) {
            size_t bodyStart = headerEndPos + 4;
            if (fullRequest.length() - bodyStart >= contentLength)
                break;
        }
    }

    request req(fullRequest);
    Response res(req);
    // std::cout << res.response << std::endl;
    send(clientSocket, res.response.c_str(), res.response.length(), 0);
    close(clientSocket);
}

int main() {
    int sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1) {
        std::cerr << "Failed to create socket\n";
        return -1;
    }

    sockaddr_in serverAddress;
    serverAddress.sin_family = AF_INET;
    serverAddress.sin_addr.s_addr = INADDR_ANY;
    serverAddress.sin_port = htons(80);

    if (bind(sock, (struct sockaddr*)&serverAddress, sizeof(serverAddress)) < 0) {
        std::cerr << strerror(errno) << std::endl;
        close(sock);
        return -1;
    }
    int i = 1;
    while (true)
    {
 
    if (listen(sock, 5) < 0) {
        std::cerr << "Listen failed\n";
        close(sock);
        return -1;
    }

    std::cout << "Server listening on port 6969...\n";

    sockaddr_in clientAddress;
    socklen_t clientAddrLen = sizeof(clientAddress);
    int clientSock = accept(sock, (struct sockaddr*)&clientAddress, &clientAddrLen);
    if (clientSock < 0) {
        std::cerr << "Accept failed\n";
        close(sock);
        return -1;
    }

    std::string num(1, i++ + '0');
    std::string response = "HTTP/1.1 200 OK\r\nContent-Length: 13\r\n\r\nHello Client";
    handleClient(clientSock);
    close(clientSock);
    }
    close(sock);
    return 0;
}