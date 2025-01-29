#include <iostream>
#include <string>
#include <cstring>   // memset
#include <cstdlib>   // exit()
#include <unistd.h>  // close()
#include <arpa/inet.h> // socket, bind, listen, accept
#include "request.hpp"

#define PORT 8080
#define BUFFER_SIZE 1024

void handleClient(int clientSocket) {
    char buffer[BUFFER_SIZE];
    memset(buffer, 0, BUFFER_SIZE);

    // Receive HTTP request
    recv(clientSocket, buffer, BUFFER_SIZE - 1, 0);
    // std::cout << "Received request:\n" << buffer << std::endl;

    request req(static_cast<std::string>(buffer));

    // Simple HTTP Response
    std::string response =
        "HTTP/1.1 200 OK\r\n"
        "Content-Type: text/plain\r\n"
        "Content-Length: 13\r\n"
        "\r\n"
        "Hello, World!";

    // Send response
    send(clientSocket, response.c_str(), response.size(), 0);

    // Close client connection
    close(clientSocket);
}

int main() {
    // 1. Create socket
    int serverSocket = socket(AF_INET, SOCK_STREAM, 0);
    if (serverSocket == -1) {
        std::cerr << "Error creating socket\n";
        return 1;
    }

    // 2. Bind to IP and port
    struct sockaddr_in serverAddr;
    memset(&serverAddr, 0, sizeof(serverAddr));
    serverAddr.sin_family = AF_INET;
    serverAddr.sin_addr.s_addr = INADDR_ANY; // Accept connections on any network interface
    serverAddr.sin_port = htons(PORT); // Convert to network byte order

    if (bind(serverSocket, (struct sockaddr*)&serverAddr, sizeof(serverAddr)) < 0) {
        std::cerr << "Error binding socket\n";
        close(serverSocket);
        return 1;
    }

    // 3. Start listening
    if (listen(serverSocket, 5) < 0) {
        std::cerr << "Error listening on socket\n";
        close(serverSocket);
        return 1;
    }

    std::cout << "Server listening on port " << PORT << "...\n";

    while (true) {
        // 4. Accept incoming connections
        struct sockaddr_in clientAddr;
        socklen_t clientLen = sizeof(clientAddr);
        int clientSocket = accept(serverSocket, (struct sockaddr*)&clientAddr, &clientLen);
        if (clientSocket < 0) {
            std::cerr << "Error accepting connection\n";
            continue;
        }

        // std::cout << "Client connected\n";

        // 5. Handle client request
        handleClient(clientSocket);
    }

    // Close server socket (never reached in this infinite loop)
    close(serverSocket);
    return 0;
}
