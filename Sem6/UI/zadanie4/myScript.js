
//kto čo komu
var fakty = [];
var dalsieFakty = [];
var pravidla = [];//nazov, podmienka[], novy result

/*
*/

vypis = false;
/*
 *  pridá fakt ak neexistuje a vráti ho ak bolo pridanie úspešné, ináč vráti false
 */

function updateTexareaFact(){
	var area = document.getElementById("facts");
	area.value = fakty.join("\n");
}

function pridajFakt(fakt){
	if(!existujeFakt(fakt)){
		fakty.push(fakt);
		updateTexareaFact();
		return fakt;
	}
	return false;
}

/*
 *  porovná fakty a podmienku a ak su rovnáke vrati pole [?PREMENNA=HODNOTA] ináč vráti false;
 */
function porovnajFakty(podmienka, fakt){
	var p = podmienka.replace("pridaj ", "").replace("vymaz ", "").split(" ");
	var f = fakt.split(" ");
	if(vypis)
		console.log("podmienka: ", podmienka, "fakt: ", fakt);
	if(p.length != f.length)
		return false;
	var res = [];
	for(i in p)
		if(p[i] != f[i]){
			if(p[i].search(/\?/) >= 0)
				res[p[i]] = f[i];
			else
				return false;
		}

	return res;
}

/*
 *  vymaže fakt ak existuje a vráti ho a ak neexistuje vráti false
 */
function vymazFakt(fakt){
	console.log("vymazava sa: " + fakt);
	for(var i in fakty)
		if(fakty[i] == fakt){
			console.log("zhoda");
			var tmp = fakty[i];
			fakty.splice(i, 1);
			updateTexareaFact();
			return tmp;
		}
	return false;
}

/*
 *  zistí či fakt už existuje v zozname faktov
 */
function existujeFakt(fakt){
	for(var i in fakty)
		if(fakty[i] == fakt)
			return true;

	return false;
}

function zlucData(res, data){
	for(var i in res){
		if(typeof data[i] === 'undefined'){
			var prida = true;
			for(var j in data)
				if(data[j] == res[i]){
					prida = false;
					break;
				}
				else if(i == j && data[j] != res[i])
					return false;
			if(prida)
				data[i] = res[i];
		}
		else if(data[i] == res[i])
			continue;
		else
			return false;
	}

	return data;
}

/*
 *  vráti pole všetkých premenných použitých v pravidle
 */
function ziskajPremenne(pravidlo){
	var result = [];
	for(var i in pravidlo[1]){
		var tmpString = pravidlo[1][i].split(" ");
		for(var j in tmpString)
			if(tmpString[j].trim().search(/\?/) == 0)
				result.push(tmpString[j].trim());
		
	}
	return new Set(result);
}

/*
 *  zistí či je splnené pravidlo zo všetkými podmienkami
 */
function jeSplnene(pravidlo, data, zhod){
	var variables = ziskajPremenne(pravidlo);

	var size = 0;
	for(var i in data)
		size++;

	if(vypis)
		console.log("pravidlo: ", pravidlo , "variables: ", variables, "data: ", data, "zhod: ", zhod);

	if(zhod != pravidlo[1].length)
		return false;

	if(Array.from(variables).length != size)
		return false;
	for(var i in variables)
		if(typeof data[i] === 'undefined')
			return false;
		else if(data[i] != variables[i])
			return false;

	return true;
}

/*
 *  vytvorí nový fakt zo splneneho pravidla
 */
function vytvorFakt(pravidlo, data, reverse = false){
	var result = pravidlo[2];

	for(var i in data)
		result = result.replace(i, data[i]);
	
	if(result.search("pridaj") == 0)
		result = pridajFakt(result.replace("pridaj ", ""))
	if(result.search("vymaz") == 0)
		result = vymazFakt(result.replace("vymaz ", ""));

	return result;
}

function preriedUzitocneFakty(uzitocneFakty, zisteneVeciPravidlom, pravidlo){
 	for(var i in zisteneVeciPravidlom){
 		var data = porovnajFakty(pravidlo[2], zisteneVeciPravidlom[i]);


 		for(var j in uzitocneFakty){
 			var zmaz = false;
 			for(var k in data)
 				if(uzitocneFakty[j].search(data[k]) >= 0){
 					zmaz = true;
 					break;
 				}
 			if(zmaz)
 				uzitocneFakty.splice(i, 1);
 		}
 	}
 	return uzitocneFakty;
}

/*
 *  skúsi najsť nový fakt a ak nový fakt nevie najsť vráti false ináč vráti nový fakt
 */
function najdiNovyFakt(){
	if(dalsieFakty.length > 0){
		var fakt = dalsieFakty[0];
		dalsieFakty.splice(0, 1);
		return fakt;
	}

 	for(var i in pravidla){//pre každé pravidlo
		var pravidlo = pravidla[i];
 		var uzitocneFakty = [];
		var zisteneVeciPravidlom = [];
		for(var k in pravidlo[1]){//a skontroluje všetky elementárne podmienky
			for(var j in fakty){//prejde všetky fakty
				var res = porovnajFakty(pravidlo[1][k], fakty[j]);
				if(vypis)
					console.log("res1: ", res);
				if(res){
					if(vypis)
						console.log(pravidlo[1][k], " == ", fakty[j]);
					uzitocneFakty.push(fakty[j]);
				}
				else{
					res = porovnajFakty(pravidlo[2], fakty[j]);
					if(vypis)
						console.log("res2: ", res);
					if(res)
						zisteneVeciPravidlom.push(fakty[j]);
				}
			}
		}
		uzitocneFakty = Array.from(new Set(uzitocneFakty));
		zisteneVeciPravidlom = Array.from(new Set(zisteneVeciPravidlom));
		uzitocneFakty = preriedUzitocneFakty(uzitocneFakty, zisteneVeciPravidlom, pravidlo);
		//if(vypis)
		console.log("pravidlo: ", pravidlo);
		console.log("veciZistenePravidlom: ", zisteneVeciPravidlom);
		while(uzitocneFakty.length >= pravidlo[1].length){
			//if(vypis)
				console.log("užitočne fakty: ", uzitocneFakty);
			var pouziteFakty = [];
			var data = [];
			for(var l in uzitocneFakty){//prejde všetky fakty
				for(var k in pravidlo[1]){
					var res = porovnajFakty(pravidlo[1][k], uzitocneFakty[l]);
					if(!res)
						continue;
					if(vypis)
						console.log("res3: ", res);
					var tmpData = zlucData(res, data);
					if(vypis)
						console.log("tmpData: ", tmpData, "fakt: ", uzitocneFakty[l]);
					if(!tmpData)
						continue;
					pouziteFakty.push(uzitocneFakty[l]);
					data = tmpData;
				}
			}
			if(vypis)
				console.log("data: ", data, "použite fakty: ", pouziteFakty, pravidlo);
			if(jeSplnene(pravidlo, data, Array.from(new Set(pouziteFakty)).length)){
				var res = vytvorFakt(pravidlo, data);
				if(res)
					return res;
			}
			uzitocneFakty.splice(0, 1);
		}
		
	}
	return false;
}

function pridajPravidlo(pravidlo){
	var area = document.getElementById("rules");
	//area.value = JSON.stringify(pravidla);
	var res = "";
	pravidla.push(pravidlo);
	for(var i in pravidla){
		res += "PRAVIDLO " + pravidla[i][0] + "\n";
		res += "AK " + pravidla[i][1].join(", ") + "\n";
		res += "TAK " + pravidla[i][2] + "\n\n";
	}
	area.value = res;
}



function init(){
	fakty = [];
	dalsieFakty = [];
	//pravidla = [];
	pridajFakt("Peter je rodic Jano");
	pridajFakt("Peter je rodic Vlado");
	pridajFakt("manzelia Peter Eva");
	pridajFakt("Vlado je rodic Maria");
	pridajFakt("Vlado je rodic Viera");
	pridajFakt("muz Peter");
	pridajFakt("muz Jano");
	pridajFakt("muz Vlado");
	pridajFakt("zena Maria");
	pridajFakt("zena Viera");
	pridajFakt("zena Eva");

	pridajPravidlo(["je surodenec", ["?X je rodic ?Y", "?X je rodic ?Z"], "pridaj ?Y je surodenec ?Z"]);
	pridajPravidlo(["je brat", ["?X je surodenec ?Y", "muz ?X"], "pridaj ?X je brat ?Y"]);
	pridajPravidlo(["je sestra", ["?X je surodenec ?Y", "zena ?X"], "pridaj ?X je sestra ?Y"]);
	pridajPravidlo(["je otec", ["?X je rodic ?Y", "muz ?X"], "pridaj ?X je otec ?Y"]);
	pridajPravidlo(["je matka", ["?X je rodic ?Y", "zena ?X"], "pridaj ?X je matka ?Y"]);	
	pridajPravidlo(["je rodic1", ["manzelia ?X ?Y", "?X je rodic ?Z"], "pridaj ?Y je rodic ?Z"]);
	pridajPravidlo(["je rodic2", ["manzelia ?Y ?X", "?X je rodic ?Z"], "pridaj ?Y je rodic ?Z"]);
}

function loadData(){
	var pravidlaString = document.getElementById("rules").value;

	pravidlaString = pravidlaString.split("PRAVIDLO ");
	
	var novePravidla = [];

	for(var i in pravidlaString){
		if(pravidlaString[i] == "")
			continue;
		var novePravidlo = [];
		var tmp = pravidlaString[i].split("\nAK ");
		novePravidlo.push(tmp[0]);
		tmp = tmp[1].split("\nTAK ");

		var rules = tmp[0].split(", ");
		var rulesRes = [];
		for(var j in rules)
			rulesRes.push(rules[j]);
		novePravidlo.push(rulesRes);
		novePravidlo.push(tmp[1].replace("\n\n", ""));
		novePravidla.push(novePravidlo);
	}

	pravidla = [];
	for(var i in novePravidla)
		pridajPravidlo(novePravidla[i]);


	var faktyString = document.getElementById("facts").value;
	faktyString = faktyString.split("\n");
	fakty = [];
	for(var i in faktyString[i])
		pridajFakt(faktyString[i]);

}

/*
pridajFakt("Simon je rodic Lenka");
pridajFakt("Simon je rodic Maroš");
pridajFakt("zena Lenka");
pridajFakt("zena Magda");
pridajFakt("Magda je rodic Gabo");
pridajFakt("Magda je rodic Kika");
pridajFakt("muz Gabo");
pridajFakt("muz Simon");
*/

function oneStep(){
	console.log("pridal sa fakt: " + najdiNovyFakt());
}

//init();

function toTheEnd(){
	while(tmp = najdiNovyFakt())
		console.log("pridal sa fakt: " + tmp);
}

//najdiNovyFaktOld();

console.log("fakty: ", fakty);
console.log("pravidla: ", pravidla);


//console.log(compareStrings("?X je rodic ?Y", "Magda je rodic Kika"));
