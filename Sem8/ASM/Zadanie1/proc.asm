INCLUDE macro.asm

TITLE Csollei_zadanie_1_PROCEDURA

JUMPS							;umozni skakanie 

MAX_BUFFER_SIZE EQU 1

DATA SEGMENT PUBLIC				;zaciatok datoveho segmentu
	EXTRN path:BYTE
	EXTRN pathLen:BYTE
	EXTRN pathMax:BYTE
	EXTRN filHand:WORD
	EXTRN filCont:BYTE

	EXTRN OPN_ERR:BYTE
	EXTRN EOL:BYTE
	EXTRN CON_MSG:BYTE
	meno_suboru     DB "text.txt", 0

	FILE_OFFSET DW 1			;offset aktualneho znaku
	REMAINING 	DW 1			
	ACT_CHAR 	DW 1			;sem sa ulozi aktualne porovnavany znak;
	NOT_CONFIRM_BUFFERS DW 1		;pocet buffrov v ktory sa nenajde koniec riadku
	CHAR_FOUND	DB 1			;priznak ci sa nasiel koniec vety
	fileBufferOffset DW 1 		;
	END_LINE_COUNTER DW 1		;pocet riadkov s koncom vety
	START_SENT_OFFSET DW 1		;offset v bufry kde 
	EOL_OFFSET_H 		DW 1
	EOL_OFFSET_L 		DW 1	
	
DATA    ENDS					;koniec datoveho segmentu

CODE SEGMENT PUBLIC				;zaciatok kodoveho segmentu
    ASSUME  CS:CODE, DS:DATA	;direktiva oznamuje
	PUBLIC printLines
printLines	PROC 
	MOV AH,3DH 					;otvorenie suboru
	MOV AL, 0					;read-only
	;MOV DX, OFFSET path		;cesta k suboru
	lea DX, meno_suboru
	INT 21H
	
	;ak sa subor nepodarilo otvorit oznami to a skoci do menu
	JNC openFileSuccessForPrint
	prints OPN_ERR
	waiting
	JMP koniec
	
	MOV END_LINE_COUNTER, 0		;nastavy pocet koncov vety na 0
	MOV NOT_CONFIRM_BUFFERS, 0	;vsetky buffre su spracovane
openFileSuccessForPrint:
	MOV filHand, AX				;v AX je kvoli funkcii 61 file handler
	;------------------------------------------------------------------
		
	
	MOV CHAR_FOUND, 0;
	
	;zaciatocna pozicia kurzora je 0
	MOV EOL_OFFSET_L, 0
	MOV EOL_OFFSET_H, 0
readFileBuffer:
	;print '+'
	;nacitanie buffera
	mov BX, filHand
	mov CX, MAX_BUFFER_SIZE		; pocet bajtov na citanie
	lea DX, filCont 			; pointer na citaci buffer
	mov AH, 3FH					; citanie suboru cez filehandle
	INT 21H
	
	;ak sa nic nenacitalo tak zavrie subor
	cmp AX,0
	JE  closeFile
	
	;ulozime pocet nacitanych znakov pre cyklus
	MOV REMAINING, AX
	
	;pocitadlo vypisanych znakov
	MOV FILE_OFFSET, 0
	;===================================================================

	
	MOV CX, REMAINING		;pocet znakov do CX pre LOOP
	MOV si,dx
	
	mov AL, [si]
	;prints EOL
	;print AL
	;prints EOL
searchEndOfSentence:
	mov AL, [si]  			; uloz znak do al
	
	;ak najdeme koniec riadku
	CMP AL, 13					;našiel sa koniec riadku
	JE  searchEndOfLine
	;CMP AL, 10					;našiel sa koniec riadku
	;JE  searchEndOfLine
		
	;ak je najdeny koniec vety tak znaky iba vypisujeme
	cmp CHAR_FOUND, 1
	JE printChar
	
	;ak najdeme koniec vety
	CMP AL, '.'
	JE  findEndOfSencence
	CMP AL, '?'
	JE  findEndOfSencence
	CMP AL, '!'
	JE  findEndOfSencence
	
	JMP nextCharacter
	
	printChar:
		print AL
		;ak najdeme koniec riadku
		JMP nextCharacter
findEndOfSencence:
	;print '*'
	;ak sa to sem dostane tak char_found je 0
	;tak ho nastavime na 1 a zvacsime pocet riadkov s koncom vety
	INC END_LINE_COUNTER
	MOV CHAR_FOUND, 1;
	;posunieme kurzor v subore na na zaciatok posledneho riadku
	
	MOV AH, 42H
	MOV BX, filHand
	MOV AL, 0				;chcem poziciu od začiatku subora
	MOV CX, EOL_OFFSET_L
	MOV DX, EOL_OFFSET_H
	INT 21H
	;skocime na nacitavanie suboru
	JMP readFileBuffer
searchEndOfLine:
	;print '-'
	
	;ak sa vypisovalo tak je na konci vety
	cmp CHAR_FOUND, 1
	JE printComplete
	
	;ak sa nenašiel znak konca vety tak sa kurzor nemeni len sa uloži
	MOV AH, 42H
	MOV BX, filHand
	MOV AL, 1				;chcem poziciu od kurzora
	MOV CX, 0
	MOV DX, 0
	INT 21H
	MOV EOL_OFFSET_L, DX
	MOV EOL_OFFSET_H, AX
	;INC EOL_OFFSET_H
	JMP readFileBuffer
printComplete:
	;prints EOL
	;print '+'
	
	;ak sa vypisovalo tak sa kurzor musi nastavit tak aby cital dalsi riadok
	MOV AH, 42H
	MOV BX, filHand
	MOV AL, 1				;chcem poziciu od kurzora
	MOV CX, 0
	MOV DX, 0
	
	;SUB DX, 8
	;INC DX
	INT 21H
	MOV EOL_OFFSET_L, DX
	MOV EOL_OFFSET_H, AX
	
	;prints EOL
	;sme na konci vety takze vsetko je v poriadku
	MOV CHAR_FOUND, 0
	
	;===================================================================
	JMP readFileBuffer
nextCharacter:
	INC SI
	INC FILE_OFFSET
	DEC REMAINING
	CMP REMAINING, 0
	JNE searchEndOfSentence
	JMP readFileBuffer
closeFile:
	print 'Z'
	MOV BX, filHand
	MOV AH, 3Eh					; funckia na zatvorenie suboru
	INT 21h
	;------------------------------------------------------------------
koniec:
    RET
printLines	ENDP

CODE ENDS    
    END