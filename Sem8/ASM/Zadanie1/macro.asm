prints MACRO t
	PUSH DX
	PUSH AX
	MOV DX, OFFSET t		;do DX vloz posunutie retazca v datovom segmente, cize relativna adresa TEXT sa ulozi do DX
	MOV AH, 09H				;funkcia na vypis retazca (write string)
	INT 21H
	POP AX
	POP DX
ENDM

printString MACRO t
	MOV AH, 09H
	LEA DX, t
	INT 21H
ENDM
	
clearScreen MACRO
	MOV AX,03H
	INT 10H
ENDM

printls MACRO stri, len		;print loaded string
	mov BH, 0
	MOV BL, len
	mov stri[bx], '$'
	
	MOV DX, stri			;do DX vloz posunutie retazca v datovom segmente, cize relativna adresa TEXT sa ulozi do DX
	MOV AH, 09H				;funkcia na vypis retazca (write string)
	INT 21H
	ENDM

	
	
inputString MACRO t
	MOV AH, 0AH
	MOV DX, OFFSET t
	INT 21H
	ENDM

printDef MACRO
	PUSH AX
	MOV AH,2				;ziadost o vystup znaku na obrazovku sluzbou cislo 2
	INT 21H					;uskutocnenie vystupu na obrazovku
	POP AX
	ENDM

print MACRO t
	PUSH DX
	MOV DL, OFFSET t
	printDef
	POP DX
	ENDM
	
printc MACRO t
	MOV DL, t
	printDef
	ENDM
	
printNum MACRO t
	MOV DX, t
	ADD DL, 48
	printDef
	ENDM
	
confirm MACRO
	prints EOL
	prints GO_MSG
	inputChar
	cmp AL, KEY_A
	JNE showMenu
	ENDM;
	
waiting MACRO
	prints EOL
	prints CON_MSG
	inputChar
	ENDM;
	
inputChar MACRO
	MOV		AH,1					;ziadost o vstup znaku z klavesnice sluzbou cislo 1
	INT		21H						;prerusenie z klavesnice pre vstup znaku
	ENDM
	
inputCharHidden MACRO
	MOV		AH,8					;ziadost o vstup znaku z klavesnice sluzbou cislo 1
	INT		21H						;prerusenie z klavesnice pre vstup znaku
	ENDM
	
inputInto MACRO t
	inputChar
	MOV OFFSET t, AL
	ENDM 

exitProgram MACRO
	MOV AH,4CH				;funkcia na ukoncenie programu a korektny navrat do MS-DOS
	INT 21H
	ENDM
	
getDate MACRO
	MOV AH, 2AH
	INT 21H
endm

getTime MACRO
	MOV AH, 2CH
	INT 21H
endm
	
printTime MACRO
	getTime ;funkcia 2CH, nasleduju jednoduche delenia pre hodiny, minuty a sekundy
	MOV hour,CH
	MOV minutes,CL
	MOV seconds,DH

	XOR AX,AX
	MOV AL,hour
	MOV BL,10
	DIV BL
	ADD AL,48
	ADD AH,48
	MOV quotient,AL
	MOV remainder,AH
	printc quotient
	printc remainder
	printc ':'

	XOR AX,AX
	MOV AL,minutes
	MOV BL,10
	DIV BL
	ADD AL,48
	ADD AH,48
	MOV quotient,AL
	MOV remainder,AH
	printc quotient
	printc remainder
	printc ':'

	XOR AX,AX
	MOV AL,seconds
	MOV BL,10
	DIV BL
	ADD AL,48
	ADD AH,48
	MOV quotient,AL
	MOV remainder,AH
	printc quotient
	printc remainder
	printc ' '

	getDate ;funkcia 2AH, nasleduju jednoduche delenia pre den, mesiac a rok
	MOV day,DL
	MOV month,DH
	MOV year,CX

	XOR AX,AX
	MOV AL,day
	MOV BL,10
	DIV BL
	ADD AL,48
	ADD AH,48
	MOV quotient,AL
	MOV remainder,AH
	printc quotient
	printc remainder
	printc '.'

	XOR AX,AX
	MOV AL,month
	MOV BL,10
	DIV BL
	ADD AL,48
	ADD AH,48
	MOV quotient,AL
	MOV remainder,AH

	printc quotient
	printc remainder
	printc '.'
	
	MOV AX,year
	MOV CX,4
	divideYear:
		XOR DX,DX
		MOV BX,10
		DIV BX
		ADD DX,48
		MOV BX,CX
		MOV yearString[BX-1],DL
		LOOP divideYear
	MOV yearString[4],'$'
	prints yearString

	ENDM