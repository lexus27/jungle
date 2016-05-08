<?php

/**
 * Контейнер разных продуктов
 * Interface ContainerInterface
 */
interface ContainerInterface{

	/**
	 * @return AvailableProductInterface[]
	 */
	public function getProducts();

}

/**
 * Interface BasketInterface
 */
interface BasketInterface extends ContainerInterface{

	/**
	 * Сумма цен на товары в корзине
	 * @return int
	 */
	public function getTotalPrice();

	/**
	 * @param MarketStockProductInterface $product
	 * @param int $count
	 * @return $this
	 */
	public function addProduct(MarketStockProductInterface $product, $count = 1);

}


/**
 * Владелец продукта
 * Interface ProductOwnerInterface
 */
interface ProductOwnerInterface{}

/**
 * Структурный: Знающий товар
 * Interface ProductAwareInterface
 */
interface ProductAwareInterface{

	/**
	 * @return ProductInterface
	 */
	public function getProduct();

}

/**
 * Структурный: Знающий владельца
 * Interface ProductOwnerAwareInterface
 */
interface ProductOwnerAwareInterface{

	/**
	 * @return ProductOwnerInterface
	 */
	public function getOwner();

}

/**
 * Множество продуктов, одного прототипа
 * Interface AvailableProductInterface
 */
interface AvailableProductInterface extends ProductAwareInterface, ProductOwnerAwareInterface{

	/**
	 * @return int
	 */
	public function getCount();

}

/**
 *
 * Interface MarketStockProductInterface
 */
interface MarketStockProductInterface extends AvailableProductInterface{

	/**
	 * @return int
	 */
	public function getPrice();

}

/**
 * Склад магазина
 * Interface MarketStockInterface
 */
interface MarketStockInterface{

	/**
	 * @return MarketStockProductInterface[]
	 */
	public function getProducts();

	/**
	 * @param $name
	 * @return MarketStockProductInterface|null
	 */
	public function findProduct($name);

}



/**
 * Товар
 * Interface ProductInterface
 */
interface ProductInterface{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return ProductCategoryInterface
	 */
	public function getCategory();

}

/**
 * Категория товара
 * Interface ProductCategoryInterface
 */
interface ProductCategoryInterface{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * Полное именование еарархии категорий корневая_категория/под_категория/конечная_категория
	 * @return string
	 */
	public function getQualifiedName();

	/**
	 * @return ProductCategoryInterface|null
	 */
	public function getParent();

}

/**
 * Магазин
 * Interface MarketInterface
 */
interface MarketInterface extends ProductOwnerInterface{

	/**
	 * Тип магазина
	 * @return MarketTypeInterface
	 */
	public function getType();

	/**
	 * @return MarketStockInterface
	 */
	public function getStock();

	/**
	 * Оформить покупку
	 * @param CustomerInterface $customer
	 * @param BasketInterface $basket
	 * @return ContainerInterface
	 */
	public function purchase(CustomerInterface $customer, BasketInterface $basket);

	/**
	 * @return BasketInterface
	 */
	public function createBasket();

}

/**
 * Тип магазина
 * Interface MarketTypeInterface
 */
interface MarketTypeInterface{

	public function getName();

}


/**
 * Покупатель, Клиент магазина
 * Interface CustomerInterface
 */
interface CustomerInterface extends ProductOwnerInterface{

	/**
	 * @return float
	 */
	public function getMoney();

	/**
	 * Покупатель подтверждает корзину товаров
	 * @param MarketInterface $market
	 * @param BasketInterface $basket
	 * @return bool
	 */
	public function purchaseAccept(MarketInterface $market, BasketInterface $basket);

}


class Client{

	protected $money = 0;

	/**
	 * @var Product[]
	 */
	protected $packet = [];

	/**
	 * @param Product $product
	 */
	public function addProductInPacket(Product $product){
		$this->packet[] = $product;
	}

	/**
	 * @return int
	 */
	public function getMoney(){
		return $this->money;
	}

	/**
	 * @param $money
	 */
	public function setMoney($money){
		$this->money = $money;
	}

	/**
	 * @return int
	 */
	public function getTotalMass(){
		$mass = 0;
		foreach($this->packet as $product){
			$mass+=$product->getMass();
		}
		return $mass;
	}

	/**
	 * @param $amount
	 */
	public function earn($amount){
		$this->money+=$amount;
	}


}

class MegoClient extends Client{

	protected $money = 100;

}

class Product{

	protected $name;

	protected $price = 0;

	protected $mass = 0;

	public function __construct($name,$price,$mass){

		$this->name = $name;
		$this->price = $price;
		$this->mass = $mass;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getPrice(){
		return $this->price;
	}

	public function setPrice($price){
		$this->price = $price;
	}

	public function getMass(){
		return $this->mass;
	}

	public function setMass($mass){
		$this->mass = $mass;
	}

}

class Market{

	protected $money = 0;

	/** @var  Product[]  */
	protected $private_products = [];

	/** @var  Product[] */
	protected $public_products = [];

	/** @var  Product[]  */
	protected $public_products_limit = 3;

	/**
	 * Market constructor.
	 * @param int $public_counts
	 */
	public function __construct($public_counts = 3){
		$this->public_products_limit = $public_counts;
	}

	/**
	 * Добавить товар
	 * @param Product $product
	 */
	public function addProduct(Product $product){
		$this->private_products[] = $product;
		$this->updateShowcase();
	}

	/**
	 * Обновить витрину
	 */
	protected function updateShowcase(){
		while(count($this->public_products) < $this->public_products_limit){
			if(!$this->private_products){
				return;
			}
			$this->public_products[] = array_pop($this->private_products);
		}
	}

	/**
	 * @param Product $product
	 * @return bool
	 */
	public function hasProduct(Product $product){
		return array_search($product,$this->public_products,true)!==false;
	}

	/**
	 *
	 * @param $name
	 * @return null|Product
	 */
	public function getProductByName($name){
		foreach($this->public_products as $product){
			if($product->getName() === $name){
				return $product;
			}
		}
		return null;
	}

	/**
	 * @return Product[]
	 */
	public function getProducts(){
		return $this->public_products;
	}

	/**
	 * @param Client $client
	 * @param Product $product
	 * @return bool
	 * @throws Exception
	 */
	public function seal(Client $client, Product $product){
		$price = $product->getPrice();
		$money = $client->getMoney();
		if($price > $money){
			throw new \Exception('Иди заработай бабла!');
		}
		$client->setMoney($money - $price);
		$this->money+=$price;
		$i = array_search($product,$this->public_products,true);
		if($i!==false){
			array_splice($this->public_products,$i,1);
			$this->updateShowcase();
			$client->addProductInPacket($product);
			return true;
		}else{
			if(array_search($product,$this->public_products,true)===false){
				throw new \Exception('Такого товара нигде нету, продавщица ничего не нашла!');
			}else{
				throw new \Exception('Товара пока нет на витрине ожидайте!');
			}
		}
	}

}


/*** Материя  */



$market = new Market(2);

$market->addProduct(new Product('Помидор',100,700));
$market->addProduct(new Product('Апельсин',466,1000));
$market->addProduct(new Product('Сосиска',20,50));
$market->addProduct(new Product('сендвич',20,200));
$market->addProduct(new Product('айфон',70000,100));
$market->addProduct(new Product('пачка сижек',10,100));
$market->addProduct(new Product('туфлиджиммичу',80000,1200));



$client = new MegoClient();
$client->zarabotat(8000);



$product = $market->getProductByName('Помидор');

if($product){
	$market->seal($client,$product);
}else{
	echo 'Аня я не нашел такого продукта';
}

echo '<pre>';
var_dump($client);
echo '</pre>';

