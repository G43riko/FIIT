/*
$(function(){
	function loadMovieDetail(imdb_id){
		$("#movie_detail_holder").load("http://localhost/movies/movies/parse/" + imdb_id);
	}	
})
*/

function updateResults(){
	var data = {
		"rating" : $("#slider-range-rating").slider("values"),
		"length" : $("#slider-range-length").slider("values"),
		"year" : $("#slider-range-year").slider("values"),
		"title"  : $("#input-title").val(),
		"search_title" : $("#es_title").is(":checked") ? 1 : 0,
		"search_tag"   : $("#es_tag").is(":checked")   ? 1 : 0,
		"search_actor" : $("#es_actor").is(":checked") ? 1 : 0,
		"search_country" : $("#es_country").is(":checked") ? 1 : 0,
		"search_genre" : $("#es_genre").is(":checked") ? 1 : 0,
		"only_prefix" : $("#es_prefix").is(":checked") ? 1 : 0
	};
	$.post("http://localhost/movies/movies/qsearch/1", data, function(d){
		var table = $("#search_results table").empty();
		var string = "<tr><th>nazov</th><th>krajny</th><th>zaner</th><th>dlzka</th><th>rok</th><th>rating</th></tr>";
		var num = d.hasOwnProperty("error") ? 0 : d.hits.total;
		$("#results_num").text("Našlo sa " + num + " filmov spĺnajúcich kritéria vyhladávania:");

		if(d.hits.total == 0){
			table.append("<tr><td>No results</td></tr>");
			return;
		}
		var dUrl = "/movies/movies/detail/";
		var yUrl = "../years/";
		var gUrl = "/movies/genres/";
		var cUrl = "/movies/countries/";
		for(i in d.hits.hits){
			movie = d.hits.hits[i]._source;
			var title = "<a href='" + dUrl + movie.movie_id + "'>" + movie.title + "</a>";
			var year = "<a href='" + yUrl + movie.year + "'>" + movie.year + "</a>";
			var genres = [];
			for(var i in movie.genres)
				genres.push("<a href='" + gUrl + movie.genres[i] + "'>" + movie.genres[i] + "</a>");
			var countries = [];
			for(var i in movie.countries)
				countries.push("<a href='" + cUrl + movie.countries[i] + "'>" + movie.countries[i] + "</a>");
			string += "<tr><td>" + title + "</td><td>" + countries + "</td><td>" + genres + "</td><td>";
			string += movie.length + "</td><td>" + year + "</td><td>" + movie.rating + "</td></tr>";
		}
		table.append(string);
	}, "JSON");
}

$(document).ready(function(){ 
	$(".sortable").tablesorter(); 
	$('.multiselectable').multiselect({
		enableFiltering: true,
        maxHeight: 380,
        buttonWidth: '480px'
	});

	$('.multiselectable-short').multiselect({
		enableFiltering: true,
        maxHeight: 380,
        buttonWidth: '222px'
	});
});

/**
 *vyhladá film v databáze
 */
function searchMovieDB(e, link = "movies/search/"){
	if(e.keyCode == 13)
		window.location = window.location.origin + "/movies/" + link + e.target.value;
}
/**
 *vyhladá herca v databáze
 */
function searchMakerDB(e){
	if(e.keyCode == 13)
		window.location = window.location.origin + "/movies/makers/search/" + e.target.value;
}

/**
 *načíta detaily o filme s IMDB
 */
function loadMovieDetail(imdb_id, button){
	$(button).addClass('active');
	$("#movie_detail_holder").load(window.location.origin + "/movies/movies/parse/" + imdb_id, function(){
		$("#movie_detail_holder").prepend("<a href='" + window.location.origin + "/movies/movies/add/" + imdb_id + "'> pridať</a>");
		$(button).removeClass('active');
	});
}

/**
 *načíta dáta o filme do vyskakovacieho okna
 */
function loadMovieModal(id){
	var link = window.location.origin + "/movies/movies/detail/" + id +"/1 div";
	$("#detailModal .modal-content").empty().load(link);
}
/**
 *načíta dáta o hercovy do vyskakovacieho okna
 */
function loadMakerModal(id){
	var link = window.location.origin + "/movies/makers/detail/" + id +"/1 div";
	$("#detailModal .modal-content").empty().load(link, function(){
		$("#moviesList").css("maxHeight", "270px");
	});
}

/**
 *vyhladá film na IMDB
 */
function searchMovie(){
	window.location = window.location.origin + "/movies/movies/searchIMDB/" + $("#search_input").val();
}

/**
 *vytvorí novú pôžičku
 */
function makeLoan(){
	var list = [];
	var movies = $(".basket-item").each(function(){
		list.push($(this).attr("alt"));
	});
	sessionStorage.clear();
	window.location = window.location.origin + "/movies/loans/add/" + list.join("_");
}

/**
 *odstráni film s košíka
 */
function removeMovie(element){
	removeMovieFromBasket(element.attr("alt"));
	element.remove();

	e = $(element);
	var p = $("#price");
	p.text(Math.round((parseFloat(p.text()) - parseFloat(e.attr("price"))) * 100) / 100);

	var n = $("#number");
	n.text(parseInt(n.text()) - 1);
}

function removeMovieFromBasket(id){
	if(sessionStorage.hasOwnProperty("basket")){
		var basket = JSON.parse(sessionStorage.getItem("basket"));
		delete basket[id];
		sessionStorage.setItem("basket", JSON.stringify(basket));
	}
}

/**
 *načíta filmy v košíku
 */
function loadMoviesFromBasket(){
	if(sessionStorage.hasOwnProperty("basket")){
		var basket = JSON.parse(sessionStorage.getItem("basket"));
		var sum = 0;
		for(i in basket){
			addMovie($("<div/>").text(basket[i][0]).attr("alt", i).attr("price", basket[i][1]), 0);
			sum += parseFloat(basket[i][1]);
		}
		$("#price").text(Math.round(sum * 100) / 100);
	}
}

/**
 *pridá film do košíka
 */
function addMovieToBasket(movie, id, price){
	var basket;
	if(sessionStorage.hasOwnProperty("basket"))
		basket = JSON.parse(sessionStorage.getItem("basket"));
	else
		basket = {};

	basket[id] = [movie, price];

	sessionStorage.setItem("basket", JSON.stringify(basket));
}

function addMovie(element, click){
	var r = $("<span/>",{css:{position:"absolute", right : "20%"}});
	$("<button/>", {css 	: {margin : "-8px"},
					text 	: "remove",
					class 	: "btn btn-default",
					onclick : "removeMovie($(this).parent().parent())"}).appendTo(r);

	var e = $(element);
	var price = parseFloat(e.attr("price"));

	
	if(sessionStorage.hasOwnProperty("basket") && sessionStorage.getItem("basket").hasOwnProperty(e.attr("alt")) && click){
		alert("film " + e.text() + " už je v košiku");
		return false;
	}	

	addMovieToBasket(e.text(), e.attr("alt"), price);	

	var li =  $("<li/>",{class 	: "list-group-item basket-item",
						 alt 	: e.attr("alt"),
						 price  : price,
						 html 	: e.text()}).append(r).appendTo($("#movies_list ul"));

	$("#moviesHints").empty();
	$("#movies_id").val("");

	var n = $("#price");
	n.text(Math.round((parseFloat(n.text()) + price) * 100) / 100);

	n = $("#number");
	n.text(parseInt(n.text()) + 1);
}

/**
 *vyprázdni košík
 */
function clearBasket(){
	sessionStorage.removeItem("basket");
}

function clearMovies(el){
	el.siblings().remove();
	$("#number").text("0");
	$("#price").text("0");
	clearBasket();
}

/**
 *načíta filmy do hintbaru
 */
function getMovies(key){
	if(key.length > 0)
		$("#moviesHints").load("/movies/movies/searchInDb/" + encodeURIComponent(key));
	else
		$("#moviesHints").empty();
}

/**
 *načíta detail o pôžičke
 */
function loadLoan(id){
	var link = window.location.origin + "/movies/loans/detail/" + id +"/1 div";
	$("#detailModal .modal-content").empty().load(link);
}