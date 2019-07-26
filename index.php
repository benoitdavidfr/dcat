<?php
/*PhpDoc:
name: index.php
title: index.php - exposition HTML+RDFa des catalogues de données
functions:
doc: |
  Chaque ressource est identifiée par un URI du type http://id.georef.eu/{catid}/{resid}
  Elle est présentée par une page HTML+RDFa index.php?cat={catid}&res={resid}
journal:
  26/7/2019:
    - première version
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
  echo "<li><a href='?cat=mtescat'>Inventaire des données du MTES & MCTRCT</a>",
    " repris de <a href='https://mtes-mct.github.io/dataroom/'>dataroom</a></li>\n";
  echo "</ul>";
  die();
}

function showObj(string $obj): string {http:///mtescat/dataset/ENPA
  if (preg_match('!^https?://localhost/yamldoc/id.php/([^/]+)/(.*)$!', $obj, $matches))
    return "<a href='?cat=$matches[1]&amp;res=".urlencode($matches[2])."' target='_blank'>$obj</a>";
  elseif (preg_match('!^https?://!', $obj))
    return "<a href='$obj' target='_blank'>$obj</a>";
  else
    return $obj;
}

// Description du catalogue
if (!isset($_GET['res'])) {
  echo "<!DOCTYPE html>\n";
  echo "<html lang=\"fr\">\n";
  echo "<head>\n";
  echo "<meta charset=\"utf-8\">\n";
  echo "<title>dcat $_GET[cat]</title>\n";
  echo "</meta>\n";
  echo "Accueil du catalogue $_GET[cat]<br>\n";
  $rdfuri = ($_SERVER['HTTP_HOST']=='localhost') ? "http://localhost/yamldoc/id.php" : "http://id.georef.eu";
  $rdfuri .= "/$_GET[cat]/catalog";
}

// Description d'une ressource du catalogue
else {
  echo "<!DOCTYPE html>\n";
  echo "<html lang=\"fr\">\n";
  echo "<head>\n";
  echo "<meta charset=\"utf-8\">\n";
  echo "<title>dcat $_GET[cat]</title>\n";
  echo "</meta>\n";
  echo "Affichage de la ressource $_GET[res] du catalogue $_GET[cat]<br>\n";
  $rdfuri = "http://localhost/yamldoc/id.php/$_GET[cat]/$_GET[res]";
}

echo "rdfuri=$rdfuri<br>\n";
$data = json_decode(file_get_contents($rdfuri), true);
echo "<pre>data="; print_r($data); echo "</pre>\n";
echo "<table border=1>";
foreach ($data as $prop => $objs) {
  if (!is_array($objs))
    echo "<tr><td>$prop</td><td>",showObj($objs),"</td></tr>\n";
  else {
    foreach ($objs as $obj)
      echo "<tr><td>$prop</td><td>",showObj($obj),"</td></tr>\n";
  }
}
echo "</table>\n";
