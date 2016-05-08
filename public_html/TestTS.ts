interface RouterInterface{

	match<Type>(): RouteInterface;
}
interface RouteInterface{

}
class Router implements  RouterInterface{

	match<Type>():RouteInterface {
		return Type;
	}


}

let router= new Router();

let b = router.match<"string">();
