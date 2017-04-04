#gcc -g -debugovacie funkcie
#dbg debugger
#18 autotool pozriet
CC = gcc
DEPS = 
CFLAGS = -I .
EXECUTABLE = uloha

%.o: %.c $(DEPS)
	$(CC) -c -o $@ $< $(CFLAGS)

install: utils.o
	$(CC) -o $(EXECUTABLE) uloha.c utils.o $(CFLAGS)
	rm ./*.o