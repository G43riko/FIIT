/*
Bernard a Chryzostom majú na stole veľa ceruziek. Keďže nemajú papier, tak si vymysleli hru, ktorou by sa zabavili. Na striedačku si berú ceruzky a ten, kto zoberie posledné zvyšné ceruzky vyhráva. Aby to ale nebolo také nudné, obmedzili si možné ťahy. Hru sa hrajú na viac krát a v každej hre môžu mať inú množinu povolených ťahov. Povolený ťah je počet ceruziek, koľko môžu zobrať na jeden ťah.
Na vstupe je niekoľko hier. Pre každú hru je najprv zadané celé číslo N - počet povolených ťahov. Nasleduje N celých čísel T_i vyjadrujúcich povolené ťahy a dve celé čísla A a B. V každej hre spočítajte pre koľko čísel X (A <= X <= B) vyhrá Bernard, ak by sa hrali hru s X ceruzkami a Bernard by ťahal ako prvý.
Obmedzenia: 
1 <= N <= 50,
1 <= T_i <= 100,
1 <= A <= B,
1 <= B <= 100000
Ukážka vstupu:
1
1
1000 1000
10
1 2 3 4 5 6 7 8 9 10
1 100000
4
1 3 7 19
1 100000
Výstup pre ukážkový vstup:
0
90910
50000
*/
// uloha-3-1.c -- Tyzden 3 - Uloha 1
// Gabriel Csollei, 3.3.2016 08:28:19

#include <stdio.h>
#include <stdlib.h>

void fillArray(int * pole, int size, int value){
	int i;
	for(i=0 ; i<size ; i++)
		pole[i] = value;
}

int main(){
	int n, i, j, a, b, sum;
	int *tahy, *kola;
	while(scanf("%d", &n) >= 1){
		n++;
		tahy = (int *)(malloc(n * sizeof(int)));
		for(i=1 ; i<n ; i++)
			scanf("%d", &tahy[i]);
		
		scanf("%d %d", &a, &b);
		b++;
		kola = (int *)(malloc(b * sizeof(int)));
		fillArray(kola, b, 0);
		sum = 0;
		for(i=1 ; i<b ; i += 1){
			for(j=1 ; j<n ; j++){
				if(i - tahy[j] == 0 || ( i - tahy[j] >= 0 && !kola[i - tahy[j]])){
					kola[i] = 1;
					break;
				}
					
			}

			if(i>= a && kola[i])
				sum++;
		}
		printf("%d\n", sum);
	}
	return 0;
}
