
#ifndef UTILS_H
#define UTILS_H

#include <stdio.h>
#include <stdlib.h>
#include <stdarg.h>
#include <string.h>

#define IS_NULL(x) (x == NULL)
#define IS_NOT_NULL(x) (!IS_NULL(x))
#define STRING(x) (IS_NULL(x) ? "NULL" : x)
#define ATTR(x, y) (IS_NULL(x) ? "NULL" : STRING(x -> y))
#define EQUAL(x, y) (strcmp(x, y) == 0)
#define TYPE_NORMAL 0
#define TYPE_SERVER 1
#define TYPE_CLIENT 2
#define ERROR(args...) fprintf(opt.errorStream, args);
#define LOG(args...) if(opt.logs){ERROR(args)}
#define PRINT(args...) dprintf(opt.outDesc, args);

typedef struct{
    FILE * handler;
    char * mode;
    char * name;
}FileHandler;

typedef struct{
    char demon;
    //FileHandler * configFile;
    FileHandler * logFile;
    FILE * errorStream;
    FILE * outputStream;
    FILE * inputStream;
    int outDesc;
    int nfds;
    char * host;
    int running;
    int port;
    int type; //0-nenastaveny, 1-server, 2-client
    char logs;
}Options;

void showOpt(Options o);

Options opt; 

/**
 * Funkcia otvor súbor a vráti štruktúru obsahujúcu názov a handler
 *
 * @param name
 * @param mode
 * @return
 */
FileHandler * openFile(char * name, char * mode);

/**
 * Funkcia dostane štruktúru s handlerom súboru a zavrú ho
 *
 * @param fileHandler
 */
void closeFile(FileHandler * fileHandler);

/**
 * Funkcia inicializuje objekt z nastaveniami
 *
 * @param opt
 */
void initOpt(Options * opt);

#endif //UTILS_H