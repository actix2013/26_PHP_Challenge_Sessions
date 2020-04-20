<?php
require_once "Article.php";

class SessionUser implements Serializable
{

    private $name = "Ananymous";
    private $passWord = "vide";
    private $dateSessionCreated = null;
    private $dateSessionExpire = null;
    private $itemsInCart = [];


    /**
     * SessionUser constructor.
     */
    public function __construct(string $name, string $password)
    {
        $this->name = $name;
        $this->passWord = $password;
        $this->setDateSessionCreated(new DateTime());
        $tempDate = $this->getDateSessionCreated();
        $this->setDateSessionExpire($tempDate->add(new DateInterval("PT1H")));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @param string $name
     * @return SessionUser
     */
    public function setName(string $name): SessionUser
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassWord(): string
    {
        return $this->passWord;
    }

    /**
     * @param string $passWord
     * @return SessionUser
     */
    public function setPassWord(string $passWord): SessionUser
    {
        $this->passWord = $passWord;
        return $this;
    }

    /**
     * @return null
     */
    public function getDateSessionCreated()
    {
        return $this->dateSessionCreated;
    }

    /**
     * @param null $dateSessionCreated
     * @return SessionUser
     */
    public function setDateSessionCreated($dateSessionCreated)
    {
        $this->dateSessionCreated = $dateSessionCreated;
        return $this;
    }

    /**
     * @return null
     */
    public function getDateSessionLastControl()
    {
        return $this->dateSessionLastControl;
    }

    /**
     * @return null
     */
    public function getDateSessionExpire()
    {
        return $this->dateSessionExpire;
    }

    /**
     * @param null $dateSessionExpire
     * @return SessionUser
     */
    public function setDateSessionExpire($dateSessionExpire)
    {
        $this->dateSessionExpire = $dateSessionExpire;
        return $this;
    }


    /**
     * @param null $dateSessionExpire
     * @return SessionUser
     */
    public function refreshDateSessionExpire(): SessionUser
    {
        $dateNow = new DateTime();
        $this->dateSessionExpire = ($dateNow->add(new DateInterval("PT1H")));
        return $this;
    }


    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->name,
            $this->passWord,
            $this->dateSessionCreated,
            $this->dateSessionExpire,
            $this->itemsInCart,

        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->name,
            $this->passWord,
            $this->dateSessionCreated,
            $this->dateSessionExpire,
            $this->itemsInCart,
            ) = unserialize($serialized);


    }

    /**
     * @return array
     */
    public function getItemsInCart(): array
    {
        return $this->itemsInCart;
    }

    /**
     * @param array $itemsInCart
     * @return SessionUser
     */
    public function setItemsInCart(array $itemsInCart): SessionUser
    {
        $this->itemsInCart = $itemsInCart;
        return $this;
    }

    /**
     * @param array $itemsInCart
     * @return SessionUser
     */
    public function addItemsInCart(Article $anArticle): SessionUser
    {
        $found=false;
        for($i=0;$i<count($this->itemsInCart);$i++){
            $idArticle=$this->itemsInCart[$i]->getId();
            $idNewArticle=$anArticle->getId();
            if($idArticle===$idNewArticle){
                $this->itemsInCart[$i]->addQte();
                $found=true;
            }
        }
        if(!$found){
            $this->itemsInCart[]=$anArticle;
        }
        return $this;

    }


}