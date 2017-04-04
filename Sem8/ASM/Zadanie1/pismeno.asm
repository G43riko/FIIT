INCLUDE macro.asm

TITLE Csollei_zadanie_1
;name echo							;pseudoinstrukcia na pomenovanie tohoto programu-modulu nie je to meno suboru,v ktorom je program ulozeny

; .model small						;deklaracia maleho modulu

JUMPS

MAX_BUFFER_SIZE EQU 30 ;MAXIMUM 64521

KEY_TAB EQU 9
KEY_ESC EQU 27
KEY_Z	EQU 90
KEY_S	EQU 83
KEY_O	EQU 79
KEY_U	EQU 85
KEY_P	EQU 80
KEY_A	EQU 97

KEY_1	EQU 49
KEY_2	EQU 50
KEY_3	EQU 51
KEY_4	EQU 52

DATA SEGMENT PUBLIC
	PUBLIC path
	PUBLIC pathLen
	PUBLIC pathMax
	PUBLIC filHand
	PUBLIC filCont
	
	PUBLIC OPN_ERR
	PUBLIC EOL
	PUBLIC CON_MSG
	
	EOL	 DB 0dh, 0ah,'$'			;definicia retazca
	MENU_WR	DB '+-----------------------------------------------+', 0dh, 0ah, '$';
	MENU_L1	DB '|Pre pouzitie programu stlac:                   |', 0dh, 0ah, '$';
	MENU_L2	DB '|   klavesu 1 pre zadanie cesty k suboru        |', 0dh, 0ah, '$';
	MENU_L3	DB '|   klavesu 2 pre vypis obsahu suboru           |', 0dh, 0ah, '$';
	MENU_L4	DB '|   klavesu 3 pre vypis dlzky suboru            |', 0dh, 0ah, '$';
	MENU_L5	DB '|   klavesu 4 pre vykonanie pridelenej ulohy    |', 0dh, 0ah, '$';
	MENU_L6	DB '|   klavesu ESC pre ukoncenie programu          |', 0dh, 0ah, '$';
	MENU_BR	DB '|                                               |', 0dh, 0ah, '$';
	MENU_A1 DB '| Autor: Gabriel Csollei    $'
	MENU_A3 DB ' |', 0dh, 0ah, '$';
	CON_MSG DB 'Pre pokracovanie stlac lubovolnu klavesu', 0dh, 0ah, '$';
	GO_MSG  DB 'Ak chcete pokracovat stlacte klavesu a', 0dh, 0ah, '$';
	OPN_ERR DB 'Chyba pri otvarani suboru', 0dh, 0ah, '$';
	
	;LINE_MAX DB 128000 DUP (?)
	OPT_1	DB 'Zadaj cestu k suboru:', 0dh, 0ah, '$'
	OPT_2	DB 'Obsah suboru:', 0dh, 0ah, '$'
	OPT_3	DB 'Velkost suboru je:', 0dh, 0ah, '$'
	OPT_3_P DB ' Znakov', 0dh, 0ah, '$'
	OPT_4	DB 'Vysledok specialnej ulohy:', 0dh, 0ah, '$'
	OPT_ESC	DB 'Dovidenia', 0dh, 0ah, '$'
	
	FIL_PRE	DB 'riadok: $'
	meno_suboru     DB "text.txt", 0
	;pismena
	
	;premenne pre datum a cas
	day DB 1
	month DB 1
	year DW 1
	yearString DB 5 DUP(?)
	hour DB 1
	minutes DB 1
	seconds DB 1
	quotient DB 1
	remainder DB 1
	
	;cesta k suboru
	pathMax DB 200					;maximalna velkost cesty
	pathLen DB 2 					;sem sa ulozia dlzky pri pouziti funkcie 0AH
	path 	DB 202 DUP(?) 			;adresa k otvaranemu suboru + NULL + $
	
	;obsah suboru
	filCont DB MAX_BUFFER_SIZE + 1 DUP(?)
	filHand DW 1
	filRead DW 1					;kolko sa nacitalo pismen zo suboru
	fileOff DW 1					;pouziva sa na zapamatanie pozicie znaku, ktory nasleduje za "nechcenym" dolarom
	
	;premenne pre pocitanie dlzky suboru
	countH	DW 1
	countL	DW 1
	filCout DB 9 DUP(?)
	
	vstup   DB 80					;bude sa citat 80 znakov
	pocet   DB 0					;tu sa ulozi ich skutocny pocet
	stri	DB 80 DUP(?)			;miesto pre citany retazec
DATA ENDS
	
ZAS SEGMENT STACK
	DW	  64 DUP(?)					;definicia 64-och slov
ZAS ENDS

CODE SEGMENT PUBLIC
	EXTRN 	printLines:PROC
	ASSUME 	DS: DATA, CS:CODE, SS:ZAS
printSize:
	prints OPT_3
	openFileForCount:
		MOV AH,3DH 					;otvorenie suboru
		MOV AL,0					;read-only
		MOV DX,OFFSET path			;cesta k suboru
		;lea DX, meno_suboru
		INT 21H
		
		;reset pocitadiel
		MOV countH,0
		MOV countL,0
			
		;ak sa subor nepodarilo otvorit oznami to a skoci do menu
		JNC openFileSuccessForCount	
		prints OPN_ERR
		waiting
		JMP showMenu
		
	openFileSuccessForCount:
		MOV filHand, AX				;v AX je kvoli funkcii 61 file handler
	filereadForCount:
		;precita zo suboru znaky subor
		mov BX, filHand
		mov CX, MAX_BUFFER_SIZE		; pocet bajtov na citanie
		lea DX, filCont 			; pointer na citaci buffer
		mov AH, 3FH					; citanie suboru cez filehandle
		INT 21H
		
		;ak sa nic nenacitalo tak zavrie subor
		cmp AX,0
		JE  filePrintCount
		
		MOV DX,AX 					;v AX je pocet precitanych bytov - hodnota sa zalohuje pre pripad, ze sucet bude vyssi ako 9999
		
		ADD DX,countL ;
		CMP DX,10000
		JE countIsEqual 			;ak je po scitani v DX presne 10000 - pretieklo, navysi sa fileCounterHigh a zresetuje Low
		JG countIsGreater 			;ak je po scitani v DX viac ako 10000 - pretieklo, navysi sa fileCounterHigh a zvysok do 10000 sa ulozi do Low
		
		MOV countL,DX 				;ak je pocet mensi ako 10000, ulozi sa vysledok scitania a normalne sa pokracuje (nikde nic nepretieklo)
		JMP filereadForCount

		countIsEqual:
			INC countH 				;navysi sa High
			MOV countL, 0 			;vynuluje sa Low
			JMP filereadForCount 	;cita sa dalej

		countIsGreater:
			MOV DX,countL 			;do DX sa ulozi doposial ziskany sucet
			incrementCount:
				DEC AX 				;znizi sa hodnota, ktoru vratila funkcia 3FH (pocet precitanych bytov)
				INC DX 				;zvysi sa hodnota v countL
				CMP DX,10000
				JNE incrementCount	;ak nebola dosiahnuta hranica pretecenia, pokracuj v inkrementovani

				INC countH			;navysi sa High
				MOV countL,AX		;v Low ostane zvysok
				JMP filereadForCount;cita sa dalej
		
		;ideme znovu citat
		jmp filereadForCount
	filePrintCount:
		MOV AX,countH 				;co delit
		MOV CX,4 					;kolkokrat
		
		divideHigh:
			XOR DX,DX 				;DX sa vynuluje
			MOV BX,10 				;cim delit
			DIV BX 					;delenie AX/BX
			ADD DX,48 				;v DX je zvysok v rozmedzi 0-9, prida sa 48 kvoli ASCII vypisu
			MOV BX,CX
			MOV filcout[BX - 1],DL
			LOOP divideHigh

		;prepis pre LOW
		MOV AX,countL 				;co delit
		MOV CX,4 					;kolkokrat
		
		divideLow:
			XOR DX,DX 				;DX sa vynuluje
			MOV BX,10 				;cim delit
			DIV BX 					;delenie AX/BX
			ADD DX,48 				;v DX je zvysok v rozmedzi 0-9, prida sa 48 kvoli ASCII vypisu
			MOV BX,CX
			MOV filcout[BX + 3],DL
			LOOP divideLow

		MOV filcout[8],'$' 			;poskladany string sa ukonci dolarom kvoli peknemu vypisu
		
		prints filcout
		prints OPT_3_P
		
	filecloseForCount:
		MOV BX, filHand
		MOV AH, 3Eh					; funckia na zatvorenie suboru
		INT 21h
	
	waiting
	JMP showMenu
loadName:
	prints OPT_1
	
	;nacitam subor
	inputString pathMax;
	
	;pridam znak konca riadku
	MOV BH, 0
	MOV BL, pathLen 
	MOV path[BX], 0
	MOV path[BX + 1], '$'
	
	;prints OPT_3_P;
	waiting
	
	JMP showMenu
printContent:
	prints OPT_2
	
	openFileForPrint:
		MOV AH,3DH 					;otvorenie suboru
		MOV AL,0					;read-only
		;MOV DX,OFFSET path			;cesta k suboru
		lea DX, meno_suboru
		INT 21H
		
		;ak sa subor nepodarilo otvorit oznami to a skoci do menu
		JNC openFileSuccessForPrint
		prints OPN_ERR
		waiting
		JMP showMenu
		
	openFileSuccessForPrint:
		MOV filHand, AX				;v AX je kvoli funkcii 61 file handler
	filereadForPrint:
		;precita zo suboru znaky subor
		mov BX, filHand
		mov CX, MAX_BUFFER_SIZE		; CX <- 1    pocet bajtov na citanie
		lea DX, filCont 			; pointer na citaci buffer
		mov AH, 3FH					; citanie suboru cez filehandle
		INT 21H						; INT21 ... citaj subor
		
		;ak sa nic nenacitalo tak zavrie subor
		cmp AX,0
		JE  filecloseForPrint
		
		;prida na koniec nacitaneho textu $
		MOV filRead, AX				;v AX je pocet bytov, ktore nacitala 3FH
		MOV BX, filRead
		MOV filCont[BX],'$' 		;na koniec sa prida dolar
		
		MOV AX,DS 					;nastavenie pre SCASB
		MOV ES,AX
		CLD 						;hladam v smere dopredu
		MOV CX,filRead 				;kolko bytov skenovat

		MOV fileOff,0 				;offset pre buffer, pre pripad vyskytu dolara - od neho bude pokracovat hladanie a od neho sa bude vypisovat

		scanString:
			MOV BX,fileOff
			LEA DI,filCont[BX] 		;pointer na prehladavany string, zaciatok za najdenym dolarom (ak je to prvy cyklus, tak zacina od 0)
			MOV AL,'$' 				;hlada sa dolar, lebo Nema uz nasli

			REPNE SCASB 			;skenuje kym nenajde dolar
			JE dollarFound
			printString filCont[BX] ;ak dolar nenaslo, vypise sa zvysok bufferu (pripadne aj cely, ak v prvom cykle nebol najdeny dolar)
			JMP continueReading

			dollarFound:
				MOV BX,fileOff
				printString filCont[BX] ;vypise sa cast stringu po najdeny, "nechceny" dolar

				MOV BX,DI 			;pozicia znaku, ktory sa nachadza ZA najdenym dolarom je v DI
				SUB BX,OFFSET filCont ;vypocita sa offset znaku za dolarom
				MOV fileOff,BX 		;a ulozi pre dalsi cyklus (aby znova neprehladaval ten isty string od zaciatku)

				print '$' 			;vypise sa nechceny, teraz uz chceny dolar ako obycajny znak

				JMP scanString 		;prehladava zvysok bu
			continueReading:
				;CMP BX,fileBufferSize ;ak 3FH precitala presne tolko znakov, kolko sa zmestilo do buffera, pokracuje v citani (lebo subor nebol precitany cely)
				JMP filereadForPrint
	filecloseForPrint:
		MOV BX, filHand
		MOV AH, 3Eh					; funckia na zatvorenie suboru
		INT 21h
	
	waiting
	JMP showMenu
pridelenaUloha:
	prints OPT_4
	CALL printLines
	waiting
	JMP showMenu
getInput:
	inputCharHidden
	prints EOL
	cmp		al, KEY_1				
	jz		loadName
	cmp		al,KEY_2				
	jz		printContent
	cmp		al,KEY_3				
	jz		printSize
	cmp		al,KEY_4			
	jz		pridelenaUloha
	cmp		al,KEY_ESC		
	je		koniec
	 
showMenu: 
    clearScreen 					;vycistenie obrazovky (video mod 80x25)
	
	;vypis menu
	prints MENU_WR
	prints MENU_L1
	prints MENU_L2
	prints MENU_L3
	prints MENU_L4
	prints MENU_L5
	prints MENU_L6
	prints MENU_BR
	
	;prints MENU_LL
	prints MENU_A1
	printTime
	prints MENU_A3
	prints MENU_WR
	jmp getInput;
	 
start:								; navestie prvej instrukcie
	MOV AX, SEG DATA				;do AX vloz adresu segmentu DATA
	MOV DS, AX						
	
	jmp showMenu
	
getInput2: 	
	
	
	prints EOL
	prints stri
	prints EOL
	
	;printls stri pocet
	prints EOL

looper:	
	inputInto DL
	cmp	 AL, KEY_ESC				;bol stlaceny ENTER?
	jz	  koniec					;ak ano, skonci program - vrat sa do MSDOS
	
	;jmp getInput

	printDef
	prints  EOL;TEXT
	jmp	 looper						;citaj dalsi znak z klavesnice
koniec:
	prints OPT_ESC
	exitProgram
CODE ENDS
	end	 start						;program bude spusteny od navestia start
