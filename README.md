
************************************************************
Principe de fontionnement : 


* Full objet. ( ou presque ) 
* La classe session manager est instancier en prmier dans le header . 
* A chaque chargement de page un objet SessionManager est creer. 
* L'objet SessionManager gerer toute l'intéligence et les action sont redirigé en fontion de l'URI demandée . 
* La classe SessionUser contiend toutes les informations en rapport avec la session dont les arcticles ajoutés au panier . 
* La SessionUser Implements la une interface Serializable ce qui permet de tranformer l'objet en string ( necessaire pour le stocker en $_SESSION)
************************************************************



Ce Template html va te permettre de réaliser ta quête sur la gestion des cookies et des sessions avec PHP.

Sont inclus :

* la page de connexion,
* la page des produits disponibles a l'achat,
* la page panier, affichant à partir des cookies les produits sélectionnés par l'utilisateur.

Tu n'as que du script PHP à fournir !

Après, si l'envie te prend de refondre le CSS, nous ne ferons rien pour t'en empêcher !
