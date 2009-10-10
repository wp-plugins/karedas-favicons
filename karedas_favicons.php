<?php
/*
Plugin Name: Karedas Favicons
Plugin URI: http://karedas.net/
Description: Adds corresponding favicon from Google Shared Stuff after every links in your posts
Version: 1.0.0
Author: Xavier Derrey
Author URI: http://karedas.net/
*/

/**
 * La fonction ajoutant les favicons
 * @param str $content contenu html
 * @return html
 */
function karedas_favicon_filtering_function($content)
{
	$list = array();
	//recup du domain du blog pour ne pas ajouter de favicon sur les liens internes
	$self_domain = str_replace('"','',preg_replace('/^http:\/\//','','http://'.$_SERVER['HTTP_HOST']));
	//recherche d'un début de lien tant qu'on en trouve
	$last = 0; //log de la position du dernier lien traité
	while($debut = strpos($content,'<a',$last)) {
		//on recherche la fin du lien trouvé
		$fin = strpos($content,'</a>',$debut);
		//on extrait le lien avec ses balises
		$lien = substr($content,$debut,($fin+4)-$debut);

		//recherche du domaine au sein du lien
		$url = array();
		$regex = '#href=(.*)( .*)?>#Usi';
		preg_match ($regex,$lien,$url);
		//on recupere le domaine
		if(sizeof($url) > 0) {
			//on vire les " que le preg_match a laissé (je hais les expressions régulières)
			$domain = str_replace('"','',$url[1]);
			//on supprime l'eventuel http://
			$domain = str_replace('http://','',$domain);
			//on ne garde que le domaine, on vire tout les sous repertoire ou fichiers de l'url
			if($res = strpos($domain,'/')) {
				$domain = substr($domain,0,$res);
			}
			//on log lien et domain a remplacer
			if($domain != $self_domain && !in_array(array($lien,$domain),$list)) {
				$list[] = array($lien,$domain);
			}
		}
		//on log la position de fin
		$last = $fin;
	}
	
	//on remplace chaque lien identifié par le lien avec le favicon
	if(sizeof($list) > 0) {
		foreach($list as $l) {
			$content = str_replace($l[0],$l[0].'<img class="favlink" alt="'.$l[1].'" title="'.$l[1].'" src="http://www.google.com/s2/favicons?domain='.$l[1].'" width="16" height="16" />',$content);
		}
	}
	//on renvoi l'article transformé
	return $content;
}


//ajout du hook sur les articles
add_filter('the_content', 'karedas_favicon_filtering_function');

?>