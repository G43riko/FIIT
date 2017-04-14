/*
 * Gabriel Csollei Zadanie 2
 *
 * Splnene bonusove ulohy:
 * + 01 (0.5 bodu) Príkaz "cat" ktorý zadaný vstup pošle bez zmeny na výstup (pre účely ladenia)
 * + 02 (2 bod) Jeden z príkazov bude využívať funkcie implementované v samostatnej knižnici, ktorá bude "prilinkovaná" k hlavnému programu.
 * - 03 (3 body) Ak je niektoré spojenie nečinné zadanú dobu, bude zrušené.
 * - 04 (1 bod) Doba nečinnosti z predchádzajúceho bodu môže byť zadaná za argumentom "-t" a/alebo ako premenná prostredia.
 * + 05 (1 bod) Ak príkaz nebude programom rozoznaný, tak celý zadaný riadok s príkazom sa vykoná ako príkaz OS (resp. shell-u).
 * + 07 (0.5 bodu) S prepínačom "-v" sa budú zobrazovať pomocné (debugg-ovacie) výpisy na štandardný chybový výstup (stderr).
 * + 08 (1 bod) Príkazy musia byť rozoznané aj ako argumenty na príkazovom riadku (e.g. -halt, -info, -run) v kombinácii s prepínačom "-c", vtedy sa vykonajú jednorazovo a program sa ukončí.
 * + 09 (1 bod) Zmysluplné použitie premennej prostredia (e.g. to, akým spôsobom sa program bude správať keď nebude zadaný ani jeden z prepínačov "-s" a "-c").
 * + 10 (5 body) Príkazy halt, quit a info sa vykonajú po zachytení zvoleného signálu (rozmyslieť klient/server).
 *!-!11 (2 body) Program s prepínačom "-s" a "-d" sa bude správať ako démon (neobsadí terminál), nebude používať štandardný vstup a výstup.
 * + 12 (1 bod) Program s prepínačom "-l" a menom súboru bude do neho zapisovať záznamy o vykonávaní (log-y).
 * + 13 (2 body) Program s prepínačom "-c" a menom súboru načíta konfiguráciu zo súboru (doba nečinnosti, log súbor, ...).
 * + 14 (1 bod) Predvolené meno konfiguračného súboru nastavené v premennej prostredia.
 * + 17 (2 body) Poriadny Makefile.
 * - 18 (3 body) Vytvorenie a použitie konfiguračného skriptu (./configure).
 * + 20 (12 bodov) Rekurzívne vypísať zoznam súborov v zadanom adresári (ako 'ls').
 otazky
 	casovy interval je na klientovy
 	co sa udeje na klientovy po quit??
 	prepínač -C?? je viac krát?	
 */
#include <utils.h>
#include <unistd.h>
#include <signal.h>
#include <time.h>
#include <errno.h>
#include <dirent.h>

#if defined(__gnu_linux__) || defined(__linux__)
	#include <sys/types.h> 
	#include <sys/socket.h>
	#include <sys/time.h>
	#include <netinet/in.h>
	#include <netdb.h>
#endif	

#define BUFFER_SIZE 21		//velkosť buffera ktorým sa odosielaju a príjmajú dáta
#define MAX_CLIENTS 5		//maximálny počet klientov
#define CHUNK_SIZE	10		//velkosť po akej sa číta súbor
#define DEF_TIMEOUT	10000	//po akom čase sa má odhlásiť socket
#define DEF_PORT 	3333	//defaultny port na ktorom prebieha komunikacia

#define SIGNAL_CONFIG SIGUSR1
#define SIGNAL_HALT SIGINT
#define SIGNAL_QUIT SIGUSR1
#define SIGNAL_INFO SIGUSR2

fd_set openedSocks;
Options opt; 
ClientData ** clients = NULL;

struct tms {
	clock_t tms_utime;  // user time
	clock_t tms_stime;  // system time
	clock_t tms_cutime; // user time of children
	clock_t tms_cstime; // system time of children
};

struct tms * startTime;
struct tms * actTime;
/****************************************ULOHY*******************************************************/

/**
 * Funkcia spusti príkaz shellu ktorý je na vstupe
 *
 * @param command
 */
void shell(char * command){
	char buf[BUFSIZ];
	FILE * ptr;

	//zalogujeme ak treba
	LOG("vola sa funkcia shell(%s)\n", STRING(command));
	
	//vytvori proces za pomoci pajpy a fork.. command sa pripoji k /bin/sh
	ptr = popen(command, "r");
	
	//ak sa nepodarilo spustiť process
	if(IS_NULL(ptr)){
		ERROR("Nepodarilo sa spustiť proces %s\n", STRING(command));
		return;
	}

	//čítame vstup
	while (IS_NOT_NULL(fgets(buf, BUFSIZ, ptr))){		//do buf ulozi vystup
		PRINT("%s", buf);
	}

	//zavrieme process
	if(pclose(ptr) == -1){
		ERROR("Nepodarilo sa zavrieť proces %s\n", STRING(command));   
	};
}

/**
 * Funkcia ukončí program
 */
void halt(void){
	//zalogujeme ak treba
	LOG("vola sa funkcia halt()\n");

	LOG("ukoncuje sa %s\n", opt.type == TYPE_SERVER ? "server" : "client");

	/*
	//ak je descriptor 0 tak sa jedna o vstup z klavesnice
	if(opt.outDesc == 0){
	*/
		//zatvori vsetky sockety s descriptorom i, okrem 0, 1, 2 - stdin, stdout, stderr
		for(int i=3 ; i<=(opt.nfds) ; i++){
			close(i);

			//zmazeme ich zo zoznamu (vymaze bit)
			FD_CLR(i, &openedSocks);
		}
	/*
	}
	else{
		//zavrieme klienta
		close(opt.outDesc);

		//odstranime ho zo zoznamu							
		FD_CLR(opt.outDesc, &openedSocks);
	}
	*/
	//zastavime program
	opt.running = 0;
}

/**
 * Funkcia odpojí a ukončé klienta
 */
void quit(void){
	//zalogujeme ak treba
	LOG("vola sa funkcia halt()\n");

	LOG("odpaja sa klient s id %d\n", opt.outDesc);

	//
	if(opt.outDesc != 0){
		//zavrieme klienta
		close(opt.outDesc);

		//odstranime ho zo zoznamu							
		FD_CLR(opt.outDesc, &openedSocks);
	}
}

/**
 * Funkcia odošle požadovanú správu do descriptora na vstupe
 *
 * @param descriptor
 * @param buffer
 */
void sendMessage(int descriptor, char * buffer){
	LOG("odosiela sa sprava %s\n", buffer);
	int n = write(descriptor, buffer, strlen(buffer));
	if (n < 0){
		ERROR("Nastala chyba pri pisani do socketu(%d)\n", descriptor);
	}
}

/**
 * Funkcia vráti PID procesu
 * @return
 */
int getPID(void){
	asm("movl $0x14,%EAX;"
			"int $0x80;");
}

/**
 * Funkcia uspí program na určitú dobu
 * @param sec
 * @param nsec
 */
void gsleep(int sec, int nsec){
	struct timespec tim, tim2;
	tim.tv_sec = sec;
	tim.tv_nsec = nsec;
	nanosleep(&tim , &tim2);
}

/**
 * Funckia skontroluje a spracuje príkaz run zadaný clientom
 *
 * @param sockfd
 * @param messageBuffer
 * @return
 */
int checkRun(int sockfd, char * messageBuffer){
	//zalogujeme ak treba
	LOG("vola sa funkcia checkRun(%d, %s)\n", sockfd, messageBuffer);

	//ak vstup začina slovom run tak sa volá prikaz
	if(START_WITH(messageBuffer, "run ")){
		LOG("subor na spustenie je: %s\n", messageBuffer + 4);

		//odstranime znak koniec riadku z názvu súboru
		messageBuffer[strlen(messageBuffer) - 1] = 0;

		//otvoríme súbor
		FileHandler * file = openFile(messageBuffer + 4, "r");
		char * buffer = malloc(CHUNK_SIZE);
		int sizeRead;
		if (IS_NULL(buffer)) {
			ERROR("nepodarilo sa alokovat pamat o velkosti %d bajtov\n", CHUNK_SIZE);
		}
		while((sizeRead = fread(buffer, 1, CHUNK_SIZE - 1, file -> handler)) > 0){
			buffer[sizeRead] = 0;
			memset(messageBuffer, 0, BUFFER_SIZE);
			strcpy(messageBuffer, RUN_CONTENT);
			messageBuffer = strcat(messageBuffer, buffer);
			gsleep(0, 500);
			sendMessage(sockfd, messageBuffer);
		}

		gsleep(0, 500);
		
		sendMessage(sockfd, RUN_FINISH);
		
		//sendMessage(sockfd, );
		free(buffer);
		closeFile(file);
		return 1;
	}
	return 0;
}

/**
 * Funkcia vypíše nápovedu
 */
void help(void){
	//zalogujeme ak treba
	LOG("vola sa funkcia help()\n");

	//vypíšeme nápovedu
	PRINT("Funkcie programu:\n");
	PRINT(" -halt: ukonci program\n");
	PRINT(" -info: zobrazi systemove informacie\n");
	PRINT(" -help: zobrazi napovedu\n");
	PRINT(" -quit: klient sa odpoji od servera a nasledne sa ukonci\n");
	PRINT(" -cat: vypise nasledujuci argument\n");
	PRINT(" -scan: vypise obsah adresara zadany ako dalsi argument\n");
	PRINT(" -run: spusti program zadany v nasledujucom argumente\n");
	PRINT("Prepinace programu:\n");
	PRINT(" -v: zapne debugovaci vypis\n");
	PRINT(" -p: nacita konfiguraciu zo suboru ktory je v nasledujucom argumente\n");
	PRINT(" -mc: zmeni maximalny pocet klientov\n");
	PRINT(" -s: program sa sprava ako server\n");
	PRINT(" -c: program sa sprava ako client\n");
	PRINT(" -i: zapne logovanie do suboru ktory je v nasledujucom argumente\n");
	PRINT(" -r: vykona funkciu ktora je v dalsom argumente a nasledke sa program ukonci\n");
}

/**
 * Funckia priradí do premenne aktualny procesorový čas
 *
 * @param input
 */
void setTimeTo(struct tms * input){
	asm("mov $0x2b, %%eax;" //inline asm na ziskanie pointra na strukturu kde je ulozene machine name
		"int $0x80;"
		:
		:"b"(input)
		);
}

/**
 * Funkcia preitoruje adresár na vstupe
 *
 * @param name
 * @param level
 */
void listdir(const char *name, int level){
    DIR * folder;
    struct dirent * entry;

	//ak je argument súbor alebo nieje prístupný tak skončíme
    if (!(folder = opendir(name)) || !(entry = readdir(folder))){
        return;
    }

    do {
		//ak sa jedná o adresár tak vypíšeme  jeho názov a rekurzívne zavoláme funkciu
        if (entry -> d_type == DT_DIR) {
            char path[1024];

			//vypíšeme do bufferu tabulatory a názov súboru
            int len = snprintf(path, sizeof(path) - 1, "%s/%s", name, entry -> d_name);

			//ukončíme retazec
            path[len] = 0;

			//ak sú je subor aktualny subor alebo predchadzajúci súbor tak pokračujeme dalej
            if (strcmp(entry -> d_name, ".") == 0 || strcmp(entry->d_name, "..") == 0){
                continue;
			}
            PRINT("%*s[%s]\n", level * 2, "", entry->d_name);
            listdir(path, level + 1);
        }
        else{
			//vypíšeme názov súboru
            PRINT("%*s- %s\n", level * 2, "", entry->d_name);
        }
    } while(entry = readdir(folder));

	//zabrieme adresár
    closedir(folder);
}

/**
 * Funkcia zavolá rekurzívnu funkciu na výpis obsahu adresára
 * @param args
 */
void scan(char * args){
	LOG("vola sa funkcia scan(%s)\n", STRING(args));
	char name[40];
	memset(name, 0, 40);
	sscanf(args, "%s", name);
	listdir(name, 0);	
}

/**
 * Funkcia vypíše systémové informácia
 */
void info(void){
	time_t seconds;
	clock_t sTotalTime;
	clock_t uTotalTime;
	struct tms *test;
	unsigned int * time = (unsigned int *)malloc(sizeof(unsigned int));;
	char * c_time_string;

	//zalogujeme ak treba 
	LOG("vola sa funkcia info()\n");

	asm("movl $0x0d,%%eax;"//inline assembler na ziskanie sekund od roku 1970
		"int $0x80;"
		:
		:"b"(time)
		);
	seconds = *time;
	PRINT("aktualny cas je: %s", ctime(&seconds));

	for(int i=0, j=0; i<1000000000 ; i++, j *= i);

	setTimeTo(actTime);
	uTotalTime = (double)(actTime -> tms_utime - startTime -> tms_utime) / CLOCKS_PER_SEC;
	sTotalTime = (double)(actTime -> tms_stime - startTime -> tms_stime) / CLOCKS_PER_SEC;
	printf("act user time: %ld (%ld)\n", actTime -> tms_utime, uTotalTime);
	printf("act system time: %ld (%ld)\n", actTime -> tms_stime, sTotalTime);
	printf("%f\n",(double)(actTime -> tms_utime - startTime -> tms_utime));
	printf("%f\n",(double)(actTime -> tms_stime - startTime -> tms_stime));


	free(time);
}

/**
 * Funkcia vypíše string ktorý jej príde na vstupe
 *
 * @param arg
 */
void cat(char * arg){
	//zalogujeme ak treba;
	LOG("vola sa funkcia cat(%s)\n", STRING(arg));

	//vypíšeme output
	PRINT("%s\n", arg);
}

/**
 * Funkcia analyzuje súbor ktorého názov príde na vstupe
 *
 * @param arg
 */
void run(char * arg){
	FILE * subor;
	char * buffer;
	unsigned int sizeRead;
	int isWord	 = 0,
		numLines = 1,
		numWords = 0,
		numChars = 0,
		i;

	//zalogujeme ak treba
	LOG("vola sa funkcia run(%s)\n", STRING(arg));

	//odstranime medzeru
	arg++;

	//odstranime znak konca riadku
	arg[strlen(arg) - 1] = 0;

	//zalogujeme ak treba
	LOG("nazov suboru po spracovanie je: %s\n", STRING(arg));

	subor = fopen(arg, "r");
	if(IS_NULL(subor)){
		ERROR("subor %s sa nepodarilo otvorit\n", STRING(arg));
		return;
	}
	buffer = malloc(CHUNK_SIZE);

	if (IS_NULL(buffer)) {
		ERROR("nepodarilo sa alokovat pamat o velkosti %d bajtov\n", CHUNK_SIZE);
	}

	while ((sizeRead = fread(buffer, 1, CHUNK_SIZE - 1, subor)) > 0){
		//ukončime reťazec
		buffer[sizeRead] = 0;

		//pripocitame pocet znakov
		numChars += sizeRead;

		//prejdeme všetky znaky
		for(i=0 ; i<sizeRead ; i++){
			//pripocitam pocet riadkov
			if(buffer[i] == '\n'){
				numLines++;
			}
			if((buffer[i] >= 'a' && buffer[i] <= 'z') || 
			   (buffer[i] >= 'A' && buffer[i] <= 'Z') || 
			   (buffer[i] >= '0' && buffer[i] <= '9')){
				if(!isWord){
					numWords++;
				}
				isWord = 1;
			}
			else{
				isWord = 0;
			}
		}
	}

	//vypíšeme vysledok
	PRINT("pocet riadkov: %d\npocet slov: %d\npocet pismen: %d\n", numLines, numWords, numChars);

	//uvolnmime buffer
	free(buffer);
	
	//zavrieme súbor
	if(fclose(subor) == EOF){
		ERROR("subor %s sa nepodarilo zatvorit\n", arg);
	}
}

/**
 * Funckia analizuje prijatý buffer zo súborom
 * @param buffer
 * @param sizeRead
 * @param descriptor
 */
void analyzeBuffer(char * buffer, int sizeRead, int descriptor){
	clients[descriptor] -> numChars += sizeRead;
	for(int i=0 ; i<sizeRead ; i++){
		//pripocitam pocet riadkov
		if(buffer[i] == '\n'){

			clients[descriptor] -> numLines++;
		}
		if((buffer[i] >= 'a' && buffer[i] <= 'z') || 
		   (buffer[i] >= 'A' && buffer[i] <= 'Z') || 
		   (buffer[i] >= '0' && buffer[i] <= '9')){
			if(!clients[descriptor] -> isWord){
				clients[descriptor] -> numWords++;
			}
			clients[descriptor] -> isWord = 1;
		}
		else{
			clients[descriptor] -> isWord = 0;
		}
	}
}

/******************************************BOTH**************************************************/
void processFileArgs(char * fileName);

/**
 * Funkcia zmení nastavenie ktoré príde ako prvý argument podla druhého argumentu
 *
 * @param arg1
 * @param arg2
 */
void setOption(char * arg1, char * arg2){
	int newPort, maxClients;

	//zalogujeme ak treba
	LOG("vola sa funkcia setOption(%s, %s)\n", STRING(arg1), STRING(arg2));

	//ak prvi argument je null tak vypíšeme chybu
	if(IS_NULL(arg1)){
		ERROR("prvy argument je null \n");
		return;
	}

	if(EQUAL(arg1, "-v")){
		if(EQUAL(arg2, "true")){
			opt.logs = 1;
			ERROR("zapnuty debugovaci mod\n");
		}
	}
	else if(EQUAL(arg1, "-l")){
		//zalogujeme ak treba
		LOG("nastavuje sa logovaci subor %s\n", STRING(arg2));

		//skontrolujeme či už nieje nastaveny 
		if(IS_NOT_NULL(opt.logFile)){
			ERROR("logovacieho suboru nenastaveny lebo uz existuje %s\n", ATTR(opt.logFile, name));
			return;
		}

		//otvoríme súbor
		opt.logFile = openFile(arg2, "w");

		//nastavime chybovy stream na zapis do suboru
		opt.errorStream = opt.logFile -> handler;

		//zalogujeme ak treba
		LOG("logovacieho suboru %s bol nastaveny\n", ATTR(opt.logFile, name));
	}
	else if(EQUAL(arg1, "-f")){
		//zalogujeme ak treba
		LOG("nacitava sa subor s konfiguraciou %s\n", STRING(arg2));

		//zpracujeme argumenty
		processFileArgs(arg2);
	}
	else if(EQUAL(arg1, "-s")){
		//zalogujeme ak treba
		LOG("aplikacia sa nastavuje na typ server\n");
		if(opt.type != TYPE_NORMAL){
			ERROR("aplikacia uz bezi ako %s\n", opt.type == TYPE_SERVER ? "server" : "client");
			return;
		}
		opt.type = TYPE_SERVER;
	}
	else if(EQUAL(arg1, "-c")){
		//zalogujeme ak treba
		LOG("aplikacia sa nastavuje na typ client\n");
		if(opt.type != TYPE_NORMAL){
			ERROR("aplikacia uz bezi ako %s\n", opt.type == TYPE_SERVER ? "server" : "client");
			return;
		}
		opt.type = TYPE_CLIENT;
	}
	else if(EQUAL(arg1, "-p")){
		//zalogujeme ak treba
		LOG("port sa nastavuje na %s\n", STRING(arg2));

		newPort = atoi(arg2);
		if(newPort == 0){
			ERROR("cislo portu nieje validne\n");
			return;
		}
		opt.port = newPort;
	}
	else if(EQUAL(arg1, "-mc")){
		LOG("maximum clientov sa nastavuje na  %s\n", STRING(arg2));
		maxClients = atoi(arg2);
		if(newPort == 0){
			ERROR("maximalny pocet klientov musi byt vasci ako 0\n");
			return;
		}
		opt.maxClients = maxClients;
	}
}

/**
 * Funckia spracuje vstup z klávesnice
 *
 * @param buff
 */
void processInput(char * buff){
	int n;
	char arg[20];

	//zalogujeme ak treba
	LOG("vola sa funkcia processInput(%s)\n", STRING(buff));

	//ziskam prvy prikaz
	sscanf(buff, "%s%n", arg, &n);

	//porovnám prikaz
	if(EQUAL("help", arg)){
		help();
	}
	else if(EQUAL("info", arg)){
		info();
	}
	else if(EQUAL("run", arg)){
		run(buff + n);
	}
	else if(EQUAL("scan", arg)){
		scan(buff + n);
	}
	else if(EQUAL("cat", arg)){
		cat(buff + n);
	}
	else if(EQUAL("halt", arg)){
		halt();
	}
	else{
		shell(buff);
	}
}

/**
 * Funckia spracuje argumenty programu ktorú do nej prídu taktiež ako argumenty
 *
 * @param argc
 * @param argv
 */
void processArguments(int argc, char **argv){
	int i, paramC = 0;

	//zalogujeme ak treba
	LOG("vola sa funkcia processArguments(%d, char **argv)\n", argc);

	//prejdeme všetky argumenty
	for (i = 1; i < argc; i++){
		//zalogujeme aktualny argument ak máme ak máme
		LOG("Spracovava sa argument %s\n", argv[i]);

		if(EQUAL(argv[i], "-v")){ //ak chceme zobraziť debugovaci výpis
			setOption("-v", "true");
		}
		else if(EQUAL(argv[i], "-i")){ //ak sa má na logovanie použiť samostatny súbor
			setOption("-i", argv[++i]);
		}
		else if(EQUAL(argv[i], "-f")){ //ak chceme načítať konfiguračny subor
			setOption("-f", argv[++i]);			
		}
		else if(EQUAL(argv[i], "-mc")){ //ak chceme zmeniť maximalne počet klientov
			setOption("-mc", argv[++i]);			
		}
		else if(EQUAL(argv[i], "-s")){ //ak aplikacia beží ako server
			setOption("-s", NULL);			
		}
		else if(EQUAL(argv[i], "-c")){ //ak aplikacia beží ako server
			setOption("-c", NULL);		  
		}
		else if(EQUAL(argv[i], "-r")){ //ak chceme iba spustiť prikaz a nasledne ukončiť program
			paramC = 1;
		}
		else if(paramC && EQUAL(argv[i], "-cat")){
			cat(argv[++i]);
			halt();
		}
		else if(paramC && EQUAL(argv[i], "-run")){
			run(argv[++i]);
			halt();
		}
		else if(paramC && EQUAL(argv[i], "-help")){
			help();
			halt();
		}
		else if(paramC && EQUAL(argv[i], "-info")){
			info();
			halt();
		}
	}
}

/**
 * Funkcia spracuje nastavenia zo súboru ktorého názov príde na vstup
 *
 * @param fileName
 */
void processFileArgs(char * fileName){
	FileHandler * file;
	char * key, *val, * line = NULL;
	size_t len = 0;
	ssize_t read;

	//zalogujeme ak treba
	LOG("vola sa funkcia processFileArgs(%s)\n", STRING(fileName));

	//otvorime logovaci subor
	file = openFile(fileName, "r");
	
	//prejdeme cely súbor riadok po riadku
	while ((read = getline(&line, &len, file -> handler)) != -1) {
		key = strtok(line, "=");
		val = strtok(NULL, "\n");
		LOG("spracuvava sa riadok %s = %s\n", STRING(key), STRING(val));

		if(EQUAL(key, "logFile")){
			setOption("-l", val);
		}
		else if(EQUAL(key, "logs")){
			setOption("-v", val);
		}
		else if(EQUAL(key, "port")){
			setOption("-p", val);
		}
		else if(EQUAL(key, "maxClients")){
			setOption("-p", val);
		}
		LOG("%s => %s\n", STRING(key), STRING(val));
	}
 	
 	//uvolnime pamäť ak treba
	if (line){
		free(line);
	}
 	
 	//zavrieme subor
	closeFile(file);
}

/******************************************OTHERS**************************************************/

void mainLoop(void);

/**
 * Funckia vypíše chybu ktorú má na vstupe a ukončí program
 *
 * @param msg
 */
void error(const char *msg){
	//vypíšeme chybu
	perror(msg);

	//skončime program
	exit(1);
}

/**
 * Funkcia spracuje prijatú správu na strane servera
 *
 * @param descriptor
 * @param socket
 * @param msg
 */
void processMessage(int descriptor, int socket, char * msg, int size){
	//zalogujeme ak treba
	LOG("vola sa funkcia processMessage(%d, %d, %s)\n", descriptor, socket, STRING(msg));

	//nastavide output do socketu
	opt.outDesc = descriptor;

	//ak sa nejedná o vstup z klávesnice a správa je obsah súboru tak ho analyzujeme
	if(descriptor > 0  && START_WITH(msg, RUN_CONTENT)){
		LOG("bola prijata cast suboru pre prikaz run\n");
		analyzeBuffer(msg + RUN_SIZE, size - RUN_SIZE , descriptor);
	}
	//ak sa nejedná o vstup z klávesnice a správa obsahuje koniec suboru tak odpovieme vysledkom analýzi
	else if(descriptor > 0  && CONTAINS(msg, RUN_FINISH)){
		LOG("bola prijata ukoncovacia sprava prikazu run\n");
		PRINT("pocet riadkov: %d\n", clients[descriptor]	-> numLines);
		PRINT("pocet slov: %d\n", clients[descriptor]		-> numWords);
		PRINT("pocet pismen: %d\n", clients[descriptor]		-> numChars);

		if(clients[descriptor] -> numLines == 0 && clients[descriptor] -> numChars > 0){
			clients[descriptor] -> numLines = 1;
		}

		clients[descriptor] -> numLines = 0;
		clients[descriptor] -> numWords = 0;
		clients[descriptor] -> numChars = 0;
	}
	else{
		//spracujeme spravu
		processInput(msg);
	}
	//nastavime output na stdout
	opt.outDesc = 0;
}

/**
 * Funckia spracováva všetky žiadosti prijaté serverom a takisto spravuje všetky spojenia
 *
 * @param portno -číslo portu na ktorom má server počúvať
 */
void doServer(int portno){
	int sockfd, newsockfd, n, i, option = 1;
	socklen_t clilen;
	char buffer[BUFFER_SIZE];
	struct sockaddr_in serv_addr, cli_addr;
   	fd_set waitingSocks; //sockety cakajuce na precitanie
 
	//otvorime socket 
	sockfd = socket(AF_INET, SOCK_STREAM, 0);
	if (sockfd < 0){ 
		ERROR("Neporadilo sa otvorit socketu\n");
	}
	//vynulujeme adresu
	bzero((char *) &serv_addr, sizeof(serv_addr));
 
	//aby som socket mohol používať aj po neúspešnom zavretí
	setsockopt(sockfd, SOL_SOCKET, SO_REUSEADDR, &option, sizeof(option));
 
	//nastavime addresu
	serv_addr.sin_family = AF_INET;
	serv_addr.sin_addr.s_addr = INADDR_ANY;
	serv_addr.sin_port = htons(portno);
 
	//bindneme socket
	if (bind(sockfd, (struct sockaddr *) &serv_addr, sizeof(serv_addr)) < 0){
		ERROR("Neporadilo sa bindnutie socketu\n");
	}
 
	//počúvame
	listen(sockfd, MAX_CLIENTS);

	clilen = sizeof(cli_addr);
 
	//vynulujeme buffer
	bzero(buffer, BUFFER_SIZE);

    //filedeskriptorom v openedSocks nastavi bity na 0
	FD_ZERO(&openedSocks);				//filedeskriptorom v openedSocks nastavi bity na 0
	FD_SET(sockfd, &openedSocks);			//nastavi bit
	FD_SET(0, &openedSocks);
	opt.nfds = sockfd > 0 ? sockfd : 0;

	while(opt.running){
		waitingSocks = openedSocks;

		//zalogujeme ak treba
		LOG("caka sa na vstup...\n");


		//skontrolujeme či bol zadaný vstup buď na stdin alebo na niektory zo socketov
		if (select(opt.nfds + 1, &waitingSocks, NULL, NULL, NULL) == -1) {
			//pri zachyteni signalu nechceme skoncitg
			if(errno != EINTR){
				error("select");
			}
		}

		//prejdeme vsetky sockety
		for (i = 0; i <= opt.nfds; i++) {
			//ak je deskriptor nastaveny
			if (FD_ISSET(i, &waitingSocks)){
				//ak je to socket ktory pocuva pripojenia
				if(i == sockfd){
					//prijmeme pripojenie
					newsockfd = accept(sockfd, (struct sockaddr *) &cli_addr, &clilen);
					if (newsockfd < 0){
						ERROR("Neporadilo sa prijatie spojenia\n");
						continue;
					}

					if(MAX_CLIENTS == opt.numClients){
						ERROR("Klient sa nemoze pripojit lebo uz je propojeny maximalny pocet klientov\n");
						continue;
					}

					//zalogujeme ak treba
					LOG("pripaja sa novy klient s id %d\n", newsockfd);

					//zväčšíme počet pripojených klientov
					opt.numClients++;
					LOG("pocet pripojenych klientov je: %d\n", opt.numClients);

					//inicializujeme objekt s klientom
					clients[newsockfd] = initClient();

					//pridame socket medzi otvorene sockety
					FD_SET(newsockfd, &openedSocks);

					//kontrola ci novy deskriptor neni vacsi ako aktualny
					if (newsockfd > opt.nfds) {
						opt.nfds = newsockfd;
					}
				}
				else{
					//vynulujeme buffer
					bzero(buffer, BUFFER_SIZE);

					//precitame spravu
					n = read(i, buffer, BUFFER_SIZE - 1);
					LOG("client[%d] poslal: %s\n", i, buffer);
					if (n <= 0){
						if(n == 0){
							ERROR("bola prijata prazdna sprava takze rusime klienta %d\n", i);
						}
						else{
							ERROR("Neporadilo sa citanie zo socketu od klienta %d\n", i);	
						}



						//znížime počet klientov
						opt.numClients--;

						//zalogujeme ak treba
						LOG("pocet pripojenych klientov je: %d\n", opt.numClients);

						//ukoncime socket
						close(i);

						//odstranime socket zo zoznamu socketov 
						FD_CLR(i, &openedSocks);
						continue;
					}

					//spracujeme prijatú správu
					processMessage(i, sockfd, buffer, n);
				}
			}
		}
 
	};

	//zalogujeme ak treba
	LOG("server sa zatvara\n");

	//zavrieme pripojenie s klientom
	close(newsockfd);
 
	//zavrieme socket
	close(sockfd);
}

/**
 * Funkcia sa pripojí k serveru a presmerováva vstup z klávesnice do serveru a odpoveď vypisuje na obrazovku
 *
 * @param host
 * @param portno
 */
void doClient(char * host, int portno){
	int sockfd, n;
	struct sockaddr_in serv_addr;
	struct hostent *server;
	char buffer[BUFFER_SIZE];
	struct timeval  timeout;
	fd_set waitingSocks;

	//vytvori socket
	sockfd = socket(AF_INET, SOCK_STREAM, 0);
	if (sockfd < 0){ 
		error("Chyba pri otvarani socketu");
	}
 	
 	//získa hosta podla adresy 
	server = gethostbyname(host);
	 
	//skontroluje ci je zadana adresa
	if (IS_NULL(server)) {
		error("Chyba pri ziskavani hosta");
	}
 	
	//vynuluje adresu servera
	bzero((char *) &serv_addr, sizeof(serv_addr));
	 
	//nastavi adresu
	serv_addr.sin_family = AF_INET;
	bcopy((char *)server -> h_addr, (char *)&serv_addr.sin_addr.s_addr, server -> h_length);
	serv_addr.sin_port = htons(portno);
 
	//pripoji sa k socketu
	if (connect(sockfd, (struct sockaddr *) &serv_addr, sizeof(serv_addr)) < 0){
		error("Chyba pri pripajani k serveru");
	}

	//filedeskriptorom v waitingSocks nastavi bity na 0
	FD_ZERO(&waitingSocks);
	FD_SET(0, &waitingSocks);
	FD_SET(sockfd, &waitingSocks);
	//stala caka na vstup
	while(opt.running){
		LOG("Cakam na vstup...\n");

		//kolko sekund chceme cakat
	    timeout.tv_sec = DEF_TIMEOUT;

	    //kolko ms chcem cakat
	    timeout.tv_usec = 0;

		//zistime ci je zadany vstup z klavesnice alebo zo socketu
		if (select(sockfd + 1, &waitingSocks, NULL, NULL, /*&timeout*/ NULL) == -1) {
			close(sockfd);
			error("Chyba pri zistovanie zadaneho vstupu");
		}

		//nieco bolo zapisane zo stdinputu tak ideme posielat spravu
		if(FD_ISSET(0, &waitingSocks)){
			//zalogujeme ak treba
			LOG("Nieco bolo zapisane zo stdinputu tak ideme posielat spravu\n");

			//vynuluje buffer
			bzero(buffer, BUFFER_SIZE);
		
			//získa vstup
			fgets(buffer, BUFFER_SIZE - 1, opt.inputStream);

			//ak chceme ukončiť spojenie
			if(EQUAL(buffer, "quit\n")){
				//zalogujeme ak treba
				LOG("Ukoncuje sa spojenie\n");

				//zavrieme socket
				close(sockfd);

				//spustime aplikáciu lokálne
				//mainLoop();

				//nechceme aby písal správu tak breakneme loop
				break;
			}
			

			//ak sa nevola prikaz run tak prepošleme vstup z klávesnice do socketu
			if(!checkRun(sockfd, buffer)){
				//napise spravu
				n = write(sockfd, buffer, strlen(buffer));
				if (n < 0){
					ERROR("Nastala chyba pri pisani do socketu\n");
				}
			}

			//ak zadal prikaz halt alebo quit tak skoci
			if(EQUAL(buffer, "halt\n") || EQUAL(buffer, "quit\n")){
				halt();
			}
		}
		else{
			/*
			LOG("Zatvara sa klient pretoze casovy limit vyprsal\n");
			halt();
			*/
		}
		
		//niečo bolo zapísané do socketu
		if(FD_ISSET(sockfd, &waitingSocks)){
			//zalogujeme ak treba
			LOG("Nieco prislo zo servera tak sa ide citat\n");

			//vynuluje buffer
			bzero(buffer, BUFFER_SIZE);

			//precita odpoved
			n = read(sockfd, buffer, BUFFER_SIZE);
			if (n <= 0){
				if(n == 0){
					ERROR("bola prijata prazdna sprava takze sa ukoncuje spojenie\n");
				}
				else{
					ERROR("Nastala chyba pri citani zo socketu\n");
				}

				//zmažeme descriptor
				FD_CLR(sockfd, &waitingSocks);

				//skončíme
				halt();
			}
		 
			//vypise odpoved
			PRINT("%s", buffer);
		}

		//filedeskriptorom v waitingSocks nastavi bity na 0
		FD_ZERO(&waitingSocks);
		FD_SET(0, &waitingSocks);
		FD_SET(sockfd, &waitingSocks);
	}
	//zavrie socket
	close(sockfd);
}

/**
 * Funcia spracuje premenné prostredia
 *
 * @param envp
 */
void processVariables(char** envp){
	LOG("vola sa funkcia processVariables(envp)\n");
	char * parameter;
	//defaultné správanie programu
	parameter = getenv("G_DEF_TYPE");
	if(IS_NOT_NULL(parameter)){
		LOG("nasla sa premenna prostredia pre typ programu z hodnotou: %s\n", parameter);
		if(EQUAL(parameter, "1")){
			opt.type = TYPE_SERVER;
		}
		else if(EQUAL(parameter, "2")){
			opt.type = TYPE_CLIENT;
		}
		else{
			opt.type = TYPE_NORMAL;
		}
	}

	//defaultný konfiguračný súbor
	parameter = getenv("G_DEF_CONFIG");
	if(IS_NOT_NULL(parameter)){
		LOG("nasla sa premenna prostredia pre defaultny konfig: %s\n", parameter);
		setOption("-f", parameter);
	}

}

/**
 * Funkcia inicializuje všetky premenné a spracuje argumenty programu
 *
 * @param argc
 * @param argv
 * @param envp
 */
void init(int argc, char **argv, char** envp){
	actTime = (struct tms*)malloc(sizeof(struct tms));


	//zalogujeme ak treba
	LOG("vola sa funkcia init(%d, argv, envp)\n", argc);

	//inicializujeme nastavenia
	initOpt(&opt);
    opt.maxClients	= MAX_CLIENTS;
    opt.port 		= DEF_PORT;

	//zpracujeme argumenty
	processArguments(argc, argv);

	//zpracujeme premenné prostredia
	processVariables(envp);

	clients = (ClientData **)malloc(sizeof(ClientData *) * (MAX_CLIENTS + 3));
	memset(clients, 0, MAX_CLIENTS + 3);
}

/**
 * Funkcia dealokuje všetky potrebné premenné
 */
void cleanUp(void){
	//zalogujeme ak treba
	LOG("vola sa funkcia cleanUp()\n");

	//ak je otvorený logovací súbor tak ho zavrieme
	if(IS_NOT_NULL(opt.logFile)){
		closeFile(opt.logFile);
	}

	//vymažeme uvolnima pamäť pre všetky klientov 
	for(int i=0 ; i<MAX_CLIENTS + 3 ; i++){
		if(IS_NOT_NULL(clients[i])){
			free(clients[i]);
		}
	}

	//ovolnime pamäť pre objekt klientov
	free(clients);
}

/**
 * Funkcia spracováva vstup z klávesnice v prípade že sa nejedna o klienta ani o server
 */
void mainLoop(void){
	int BUFF_SIZE = 200;
	char buff[BUFF_SIZE];

	//zalogujeme ak treba
	LOG("vola sa funkcia mainLoop()\n");

	//nacitavame argumeny kym program bezi
	while(opt.running){
		//zalogujeme ak treba
		LOG("cakam na vstup...\n");
		//nacitam cely riadko
		fgets(buff, BUFF_SIZE - 1, opt.inputStream);
		
		//spracujeme input
		processInput(buff);
	}
	LOG("skoncil loop v mainLoope\n");
}

/**
 * Funckia spracuje prijaté signály
 */
void signalHandler(int signo){
	if (signo == SIGNAL_INFO){
		LOG("received signal SIGNAL_INFO = %d\n", SIGNAL_INFO);
		info();
	}
	else if (signo == SIGNAL_QUIT){
		LOG("received signal SIGNAL_QUIT = %d\n", SIGNAL_QUIT);
	}
	else if (signo == SIGNAL_HALT){
		LOG("received signal SIGNAL_HALT = %d\n", SIGNAL_HALT);
		
		halt();
		
		cleanUp();
		
		//zalogujeme ak treba
		LOG("program konci\n");
		
		exit(EXIT_SUCCESS);
	}
	else if (signo == SIGNAL_CONFIG){
		LOG("received signal SIGNAL_CONFIG = %d\n", SIGNAL_CONFIG);
	}
}

/**
 * Funckia priradí priradí handlere k prijatým signálom
 */
void setSignalHandlers(void){
	if(signal(SIGNAL_CONFIG, signalHandler) == SIG_ERR){
		ERROR("can't catch SIGNAL_CONFIG\n");
	}
	if(signal(SIGNAL_HALT, signalHandler) == SIG_ERR){
		ERROR("can't catch SIGNAL_HALT\n");
	}
	if(signal(SIGNAL_QUIT, signalHandler) == SIG_ERR){
		ERROR("can't catch SIGNAL_QUIT\n");
	}
	if(signal(SIGNAL_INFO, signalHandler) == SIG_ERR){
		ERROR("can't catch SIGNAL_INFO\n");
	}
}

/**
 * Main funkcia
 *
 * @param argc
 * @param argv
 * @param envp
 * @return
 */
int main(int argc, char **argv, char** envp){
	if(CHUNK_SIZE >= BUFFER_SIZE + 10){
		ERROR("CHUNK_SIZE musi byt aspon o 11 meni ako BUFFER_SIZE\n");
		return 0;
	}
	//uložime aktuálny čas procesoru
	startTime = (struct tms*)malloc(sizeof(struct tms));
	setTimeTo(startTime);

	setSignalHandlers();
	//inicializujeme aplikaciu
	printf("pid: %d\n", getPID());
	init(argc, argv, envp);

	//spustime aplikaciu podla pozadovaneho ty
	switch(opt.type){
		case TYPE_NORMAL: //loop ktory caka na parametre z klavesnice
			mainLoop();
			break;
		case TYPE_SERVER: //aplikacia caka na vstupy z portu
			doServer(opt.port);
			break;
		case TYPE_CLIENT: //aplikacia sa pripoji k serveru a posiela prikazy
			doClient(opt.host, opt.port);
			break;
	}

	cleanUp();

	//zalogujeme ak treba
	LOG("program konci\n");

	//skoncime
	exit(EXIT_SUCCESS);
}