<?php
/*PhpDoc:
name: index.php
title: index.php - exposition HTML+JSON-LD des catalogues de données
functions:
doc: |
  Chaque ressource est identifiée par un URI du type http://id.georef.eu/{catid}/{resid}
  Elle est présentée par une page HTML+RDFa index.php?cat={catid}&res={resid}
journal:
  29/7/2019:
    - intégration du JSON-LD
  26/7/2019:
    - première version
    - la visualisation fonctionne en localhost et sur georef.eu
    - il reste à la paufiner et à intégrer les JSON-LD
*/
//print_r($_SERVER);

// Liste des catalogues
if (!isset($_GET['cat'])) {
  echo "<!DOCTYPE html>\n";
  echo "<html lang=\"fr\">\n";
  echo "<head>\n";
  echo "<meta charset=\"utf-8\">\n";
  echo "<title>dcat accueil</title>\n";
  echo "</meta>\n";
  echo "Accueil donnant accès aux différents catalogues<br>\n";
  echo "<ul>\n";
  echo "<li><a href='?cat=cdmet'>Catalogue des données des ministères de l'environnement et des territoires</a>",
    " repris de <a href='https://mtes-mct.github.io/dataroom/'>dataroom</a></li>\n";
  echo "</ul>";
  die();
}

// retourne la chaine à afficher pour un obj pour affichage d'un lien interne ou externe
function showObj(string $obj): string {
  if (preg_match('!^http://(localhost/yamldoc/id.php|id.georef.eu)/([^/]+)/(.*)$!', $obj, $matches)) // lien interne
    return "<a href='?cat=$matches[2]&amp;res=".urlencode($matches[3])."'>$obj</a>";
  elseif (preg_match('!^(https?://[^ ]+)!', $obj, $matches)) // lien externe
    return "<a href='$matches[1]' target='_blank'>$obj</a>";
  else
    return $obj;
}

// Description du catalogue ou d'une ressource du catalogue
$cat = $_GET['cat'];
$resource = $_GET['res'] ?? null;
$rdfuri = ($_SERVER['HTTP_HOST']=='localhost') ? "http://localhost/yamldoc/id.php" : "http://id.georef.eu";
$rdfuri .= "/$cat/".($resource ?? 'catalog');

//echo "rdfuri=$rdfuri<br>\n";
$data = json_decode(file_get_contents($rdfuri), true);

echo "<!DOCTYPE html>\n";
echo "<html lang=\"fr\">\n";
echo "<head>\n";
echo "<meta charset=\"utf-8\">\n";
echo "<title>dcat $_GET[cat]</title>\n";
echo "<script type=\"application/ld+json\">\n";
echo json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
echo "\n</script>\n";
echo "</meta></head>\n";
if (!$resource)
  echo "Accueil du catalogue $cat<br>\n";
else
  echo "Affichage de la ressource $resource du catalogue $cat<br>\n";
//echo "<pre>data="; print_r($data); echo "</pre>\n";
echo "<table border=1>";
foreach ($data as $prop => $objs) {
  if ($prop == '@context')
    echo "<tr><td>$prop</td><td>$objs</td></tr>\n";
  elseif (!is_array($objs))
    echo "<tr><td>$prop</td><td>",showObj($objs),"</td></tr>\n";
  else {
    foreach ($objs as $obj)
      echo "<tr><td>$prop</td><td>",showObj($obj),"</td></tr>\n";
  }
}
echo "</table>\n";
