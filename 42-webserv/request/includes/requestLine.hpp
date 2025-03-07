#pragma once

#include "parsFuncs.hpp"

class requestLine
{
    private :
        std::string FullTarget;
        MethodType method;
        std::string reqTarget;
        std::string httpVers;
        std::map<std::string, std::string> params;
        void splitParamsFromReqTarget();
    public :
        //constructer
        requestLine(){};
        requestLine(const std::string requestLine);
        //setters
        void setMethod(const std::string method);
        void setReqTarget(const std::string url);
        void setHttpVers(const std::string vers);
        //geters
        MethodType &getMethod();
        std::string &getReqTarget();
        std::string &getReqFullTarget();
        std::string &getHttpVers();
        std::map<std::string, std::string> &getParams();
};
