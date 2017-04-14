
#ifndef UTILS_H
#define UTILS_H

#include <stdio.h>
#include <stdlib.h>
#include <stdarg.h>
#include <string.h>


#ifndef _STRING_H_
#include <string.h>
#endif
#define START_WITH(x, y) (strstr(x, y) == x ? 1 : 0)
#define CONTAINS(x, y) (strstr(x, y) != NULL ? 1 : 0)

#define IS_NULL(x) (x == NULL)
#define IS_NOT_NULL(x) (!IS_NULL(x))
#define STRING(x) (IS_NULL(x) ? "NULL" : x)
#define ATTR(x, y) (IS_NULL(x) ? "NULL" : STRING(x -> y))
#define EQUAL(x, y) (strcmp(x, y) == 0)
#define TYPE_NORMAL 0
#define TYPE_SERVER 1
#define TYPE_CLIENT 2
#define RUN_CONTENT "######"
#define RUN_SIZE 6 //počet znakov v premennej RUN_CONTENT
#define RUN_FINISH "******"
#define ERROR(args...) fprintf(opt.errorStream, args);
#define LOG(args...) if(opt.logs){ERROR(args)}
#define PRINT(args...) dprintf(opt.outDesc, args);

typedef struct{
    FILE * handler;
    char * mode;
    char * name;
}FileHandler;

typedef struct{
    int isWord;
    int numLines;
    int numWords;
    int numChars;
}ClientData;

ClientData *  initClient(void);

typedef struct{
    char demon;
    //FileHandler * configFile;
    FileHandler * logFile;
    FILE * errorStream;
    FILE * outputStream;
    FILE * inputStream;
    int outDesc;
    int nfds;
    int maxClients;
    int numClients;
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