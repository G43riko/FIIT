#include <utils.h>

FileHandler * openFile(char * name, char * mode){
    //zalogujeme ak treba
    if(opt.logs){
        ERROR("Otvarame subor %s[%s]\n", name, mode);
    }
    FileHandler * file = (FileHandler *)malloc(sizeof(FileHandler));
    file -> mode = mode;
    file -> name = name;
    file -> handler = fopen(name, mode);
    if (IS_NULL(file -> handler)){
        ERROR("Subor %s sa nepodarilo otvorit v mode %s\n", file -> name, file -> mode);
    }
    return file;
}

void showOpt(Options o){
    PRINT("running: %d\n", opt.running);
    PRINT("demon: %d\nlogs: %d\n", o.demon, o.logs);
    PRINT("logFile: %s\n", ATTR(o.logFile, name));
}

void closeFile(FileHandler * fileHandler){
    //zalogujeme ak treba
    if(opt.logs){
        ERROR("Zatvarame subor %s\n", fileHandler -> name);
    }

    if(fclose(fileHandler -> handler) == -1){
        ERROR("Subor %s sa nepodarilo zavriet\n", fileHandler -> name);
    };
 
    free(fileHandler);
}

void initOpt(Options * opt){
    opt -> port         = 3333;
    opt -> type         = TYPE_NORMAL;
    opt -> host         = "localhost";
    opt -> demon        = opt -> logs = opt -> running = opt -> outDesc = opt -> nfds = 0;
    opt -> running      = 1;
    opt -> logFile      = NULL;
    opt -> inputStream  = stdin;
    opt -> errorStream  = stderr;
    opt -> outputStream = stdout;
}