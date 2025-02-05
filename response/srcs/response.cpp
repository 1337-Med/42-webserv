#include "response.hpp"

Response::Response(request r)
{
    req = r;
    if (req.getReqLine().getMethod() == GET)
    {
        get();
    }
    else if (req.getReqLine().getMethod() == POST)
    {
        post();
        std::cout << "post method" << std::endl;
    }
}

void Response::get()
{
    std::ifstream file("." + req.getReqLine().getReqTarget());
    if (!file.is_open())
    {
        std::ifstream file404("./404.html");
        std::cout << req.getReqLine().getReqTarget() << std::endl;
        if (!file404.is_open())
        {
            std::cerr << "error opening 404.html" << std::endl;
            return;
        }
        std::cerr << "error with the file" << std::endl;
        std::ostringstream fileContent;
        fileContent << file404.rdbuf();
        body = fileContent.str();
        header = "HTTP/1.1 404 KO\r\nContent-Length: ";
        std::stringstream ss;
        ss << body.length();
        response = header + ss.str(); + "\r\n\r\n" + body;
        
        file404.close();
        return ;
    }
    std::string str;
    std::ostringstream fileContent;
    fileContent << file.rdbuf();
    body = fileContent.str();
    header = "HTTP/1.1 200 OK\r\nContent-Length: ";
    std::stringstream ss;
    ss << body.length();
    std::string lengthStr = ss.str();
    response = header + lengthStr + "\r\n\r\n" + body;
    file.close();
}
void Response::post()
{
    std::cout << req.getReqLine().getReqTarget() << std::endl;
    std::map<std::string, std::string>::iterator it = req.getReqLine().params.begin();
    while (it != req.getReqLine().params.end())
    {
        std::cout << it->first << " : " << it->second << std::endl;
        it++;
    }
    body ="submited";
    header = "HTTP/1.1 200 OK\r\n"
            "Content-Type: text/html\r\n"
            "Content-Length: " + std::to_string(body.length()) + "\r\n"
            "\r\n";
    // std::stringstream ss;
    // ss << body.length();
    // std::string lengthStr = ss.str();
    // response = header + lengthStr + "\r\n\r\n" + body;
    response = header + body;
}

void Response::DELETE()
{

}
void Response::methodFilter()
{
    // if ()
}

