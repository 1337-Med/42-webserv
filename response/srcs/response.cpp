#include "response.hpp"

Response::Response(request r)
{
    // std::cout << "req" << std::endl;
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
    std::cout << "|" << req.getReqLine().getReqTarget() << "|" << std::endl;
    std::ifstream file;
    if (req.getReqLine().getReqTarget() == "/")
        file.open("./index.html");
    else
        file.open("." + req.getReqLine().getReqTarget());
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
        response = header + ss.str() + "\r\n\r\n" + body;
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

    cgi c(req);

    c.runCgi();


    // response += "HTTP/1.1 200 OK\r\n";
    response = c.getResponse();
}

void Response::DELETE()
{

}
void Response::methodFilter()
{
    // if ()
}

