<?php
require_once "SessionUser.php";
require_once "Article.php";

class SessionManager
{
    const SESSION_MANAGER_OBJECT_NAME = "userObjectName";
    private $validSession = false;
    private $managedSession = null;
    private $indexAccess = false;
    private $loginAccess = false;
    private $cartAccess = false;
    private $curentPage = "/";
    private $userName="anonymous";

    /**
     * ***** La classe session manager est un gestionnaire global pour toute les pages *****.
     * Une instance doit etre cree a chaque chargement de la page .
     *
     *   Tout le traitement est effectué en objet , pour stocker les objets en session  , l'interface serialisable est implémenté sur la claszse SessionUser .
     *
     *
     *  - 1 identification de l'url  qui est demandée --> permet d'effectuer le traitement assicié a la page demandée .
     *  - 2 verifie si il existe une variable $_SESSION:userObjectName et effetue un traitement en fontion
     *  - 3 serie de traitement en fontion de l'url  demandée selon les criteres de l'enoncé
     * @param null $sessionUser si non null , cette session remplace la session existante .
     */
    public function __construct(string $curentURI, SessionUser $sessionUser = null)
    {


        $this->curentPage = parse_url($curentURI)['path'];


        if (strcmp($this->curentPage, "/") == 0 || strcmp($this->curentPage, "/index.php") == 0) {
            $this->indexAccess = true;
        } elseif ($this->curentPage === "/cart.php") $this->cartAccess = true;
        elseif ($this->curentPage === "/login.php") $this->loginAccess = true;
        $this->sessionObjectName = self::SESSION_MANAGER_OBJECT_NAME;


        if (!isset($sessionUser)) {
            $this->managedSession = $this->loadObjectSession($this->getSessionObjectName());
            if (isset($this->managedSession)) {
                $this->validSession = true;
                $this->setUserName( $this->getManagedSession()->getName());
            } else $this->validSession = false;
        }
        else {
            $this->managedSession = $sessionUser;
            $this->validSession = true;
        }

        if ($this->indexAccess) {
            $this->traitementPageIndex();
        } elseif ($this->cartAccess) {
            $this->traitementPageParts();
        } elseif ($this->loginAccess) {
            $this->traitementPageLogin();

        }

    }


    /**
     * cette fontion regarde si une session existe , si  oui chargement , sinon retourne null  volontairement .
     * @return SessionUser
     */
    private
    function loadObjectSession()
    {
        $aSessionUser = null;
        if (isset($_SESSION[$this->getSessionObjectName()])) {
            $aSessionUser = unserialize($_SESSION[$this->getSessionObjectName()]);
        }
        return $aSessionUser;
    }



    /**
     * fontion de rediretion vers la page passer en parametre
     * @param string $urlPageDestination chemin de redirection depuis la racine du  site
     */
    public
    function redirectionHeaderType(string $urlPageDestination)
    {
        header("Location:" . $urlPageDestination);
    }

    /*
     * verifie si la session en cours est valable et si  name est pas vide
     */
    public
    function controlSession(): bool
    {
        if (empty($this->managedSession->getName())) {
            return false;
        }
        return true;

    }

    /**
     * retourne la valeur du  champ a recherche dans la variable $_SESSION
     * @return string
     */
    public
    function getSessionObjectName(): string
    {
        return $this->sessionObjectName;
    }


    /**
     * @return bool
     */
    public
    function isValidSession(): bool
    {
        return $this->validSession;
    }

    /**
     * retourne la session en cours de gestion ( actuellement , une seule sessiion)
     * @return SessionUser
     */
    public
    function getManagedSession()

    {
        return $this->managedSession;
    }

    public function addItemToCurentSession(Article $article)
    {

        if($this->validSession){
            $this->getManagedSession()->addItemsInCart($article);
            $this->writeObjectSession($this->getManagedSession());

        }else {
            $this->redirectionHeaderType("/login.php?request=anonymous");
        }


    }

    /**
     * retourne le username qui est une copie du  username contenue dans l'objet SessionUser, utile pour afficher le nom d'utilisateur dans le head
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return SessionManager
     */
    public function setUserName(string $userName): SessionManager
    {
        $this->userName = $userName;
        return $this;
    }



    /*
     * Execute tout le traitement destiné a la page index , cette fontion est apellée par le constructeur
     */
    private function traitementPageIndex()
    {
        if(!empty($_GET["add_to_cart"])){
            $id1=(int)$_GET["add_to_cart"];

            $name=$_GET["name"];
            $description=$_GET["description"];
            $anAnticle=new Article((int)$id1,$name);
            $this->addItemToCurentSession($anAnticle);
            $_GET["add_to_cart"] = null;
            $_GET["name"]=null;
            $_GET["description"]=null;
            $this->redirectionHeaderType("/");

        }
    }

    /*
     * Execute tout le traitement destiné a la page login , cette fontion est apellée par le constructeur
     */
    private function traitementPageLogin()
    {
        if($this->isValidSession()){
               $this->redirectionHeaderType("/");
        }
        if ($_POST) {
                $this->createNewSesionUser();
                $this->redirectionHeaderType("/");
        }

        if($this->validSession) {
            if (!empty($_GET["request"])) {
                if ($_GET["request"] === 'deconnect') {
                    $this->disconect();
                    header("Location:/index.php");
                    // detection  si  un utilisateur venant du  menu  clique sur login alors que il est deja authentifié
                    // redirection  vers index. le nom de l'utilisateur est passser en parametre via le lien login dans le head
                }
            }
        }

        if (!$_GET["request"]==="anonymous") {
            // l'utilisateur est deja identifier , revoi vers l'index ( pas secutre :-( )
              if($this->isValidSession()){
               $this->setUserName($this->getManagedSession()->getName());
            }
            $this->redirectionHeaderType("/");
        }
  }


    private function traitementPageParts()
    {
        if(!$this->isValidSession()){
            $this->redirectionHeaderType("/");
        }

    }

    function disconect (){
        session_destroy();
    }

    /*
    * Execute tout le traitement destiné a la page login , cette fontion est apellée par le constructeur
    */
    function createNewSesionUser(){
        $session = new SessionUser($_POST["loginname"], "password");
        $this->managedSession=$session;
        $this->validSession=true;
        $this->writeObjectSession($session);
    }

    /**
     * cette fontion  a pour but de serialiser l'objet passer en parametre  puis de l'enregistrer en session.
     * @param SessionUser $aSessionUser
     * @return SessionUser
     */
    private
    function writeObjectSession(SessionUser $aSessionUser)
    {

        if ($this->isValidSession()) {
            $serialUser = serialize($aSessionUser);
            $_SESSION[self::SESSION_MANAGER_OBJECT_NAME] = $serialUser;
        }
        return $aSessionUser;
    }

    /**
     * cette fonction  a pour but de prolonger la durée de la session   a chaque appel.
     * @return bool
     */
    public
    function updateManagedSessionExpire(): bool
    {
        $returnValue = false;
        if ($this->isValidSession()) {
            $session = $this->getManagedSession()->refreshDateSessionExpire();
            $this->writeObjectSession($session);
            $returnValue = true;
        }
        return (boolean)$returnValue;
    }


}