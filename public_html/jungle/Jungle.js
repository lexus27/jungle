/**
 * @Jungle-SingleTone
 * @type {{
 *     toBool,
 *     toInt,
 *     toFloat,
 *     toString,
 *     isSet,
 *     isEmpty,
 *     isNum,
 *     isInteger,
 *     isFloat,
 *     isString,
 *     isArray,
 *     isObject,
 *     isInstance,
 *     isData,
 *     isIterable,
 *     isFn,
 *     isCallable,
 *     callCallable,
 *     isClass,
 *     isBool,
 *     count,
 *     compareFn,
 *     clone,
 *     inArray,
 *     apply,
 *     applyIf,
 *     foreach,
 *     collect,
 *     Base,
 *     setSafeInstanceProperty,
 *     hasMixinInClass,
 *     collectClassDefaultProperties,
 *     isClassSystemProperty,
 *     isInstanceSystemProperty,
 *     define,
 *     test
 * }}
 */
var Jungle = (function(){


	var emptyFn = function(){};

	var tester = null;

	return {

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		toBool: function(v){
			if(!this.isBool(v) && (this.isObject(v) || this.isArray(v))){
				return !this.isEmpty(v);
			}
			return !!v;
		},

		/**
		 *
		 * @param v
		 * @returns {Number}
		 * @param useRound
		 */
		toInt: function(v,useRound){
			useRound = useRound?true:false;
			if(this.isArray(v)){
				return v.length===1?this.toInt(v[0]):NaN;
			}
			if(useRound){
				var val = parseFloat(v);
				return isNaN(val)?NaN:Math.round(val);
			}else{
				return parseInt(v);
			}

		},

		/**
		 *
		 * @param v
		 * @returns {Number}
		 */
		toFloat: function(v){
			if(this.isArray(v)){
				return v.length===1?this.toFloat(v[0]):NaN;
			}
			return parseFloat(v);
		},

		/**
		 *
		 * @param v
		 * @returns {string}
		 */
		toString: function(v){
			if(!this.isString(v)){
				if(v === null){
					return '';
				}
				if(v.toString && v.toString !== Object.prototype.toString){
					return v.toString();
				}
				if(this.isObject(v)){
					if(v.toString !== Object.prototype.toString){
						return v.toString();
					}else{
						//console.log('Jungle.toString('+v+') he is trying to convert (object) to string');
						//throw new TypeError('Jungle.toString('+v+') he is trying to convert (object) to string');
						return '';
					}
				}else if(isNaN(v)){
					//console.log('Jungle.toString('+v+') he is trying to convert (NaN) to string');
					//throw new TypeError('Jungle.toString('+v+') he is trying to convert (NaN) to string');
					return '';
				}else if(this.isArray(v)){
					//console.log('Jungle.toString('+v+') he is trying to convert (Array) to string');
					//throw new TypeError('Jungle.toString('+v+') he is trying to convert (Array) to string');
					return v.length===1?this.toString(v[0]):'';
				}else if(this.isFn(v)){
					//console.log('Jungle.toString('+v+') he is trying to convert (Function) to string');
					//throw new TypeError('Jungle.toString('+v+') he is trying to convert (Function) to string');
					return '';
				}else if(!this.isSet(v)){
					//console.log('Jungle.toString('+v+') he is trying to convert (undefined) to string');
					//throw new TypeError('Jungle.toString('+v+') he is trying to convert (undefined) to string');
					return '';
				}
			}
			return ""+v+"";
		},


		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isSet: function(v){
			return typeof v !== "undefined";
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isEmpty: function(v){
			return (this.isIterable(v) && (v instanceof Array?v.length:Object.keys(v).length)===0) ||
				(this.isFn(v) && this.compareFn(v,emptyFn)) ||
				typeof(v) === "undefined" || v === '' || v === null || v === NaN;
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isNum: function(v){
			var f = parseInt(v);
			return typeof f === "number" && !isNaN(f);
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isInteger: function(v){
			return Number(v)===v && v%1===0;
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isFloat: function(v){
			return Number(v)===v && v%1!==0;
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isString: function(v){
			return typeof v === 'string';
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isArray: function(v){
			return v instanceof Array;
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isObject: function(v){
			return typeof v === 'object' && v  !== null && !(v instanceof Array);
		},

		/**
		 *
		 * @param v
		 * @param of
		 * @returns {*|boolean}
		 */
		isInstance: function(v,of){
			if(this.isObject(v) && Object.getPrototypeOf(v) !== Object.prototype){
				if(typeof of !== "undefined"){
					var superclass = this.isInstance(of)?of.__class__:of;
					var targetClass = v.__class__;
					return v instanceof superclass || this.hasMixinInClass(targetClass, superclass);
				}else{
					return true;
				}
			}else{
				return false;
			}
		},

		/**
		 *
		 * @param v
		 * @returns {*|boolean}
		 */
		isData: function(v){
			return (this.isObject(v) && Object.getPrototypeOf(v) === Object.prototype);
		},

		/**
		 *
		 * @param v
		 * @returns {*}
		 */
		isIterable: function(v){
			return this.isArray(v) || this.isData(v);
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isFn: function(v){
			return typeof v === 'function';
		},

		/**
		 * @param callable
		 * @returns {boolean}
		 */
		isCallable: function(callable){
			return this.isFn(callable) ||
				(this.isArray(callable) && this.isFn(callable[0])) ||
				(this.isObject(callable) && this.isFn(callable['fn']));
		},

		callCallable: function(callable,defaultReturn,args){
			args = this.isArray(args)?args:[];
			if(this.isFn(callable)){
				return callable.apply(window,args);
			}else if(this.isArray(callable)){
				if(this.isFn(callable[0])){
					return callable[0].apply(callable[1] || window,args);
				}else{
					return defaultReturn;
				}
			}else if(this.isObject(callable)){
				if(this.isFn(callable['fn'])){
					return callable['fn'].apply(callable['scope'] || window,args);
				}else{
					return defaultReturn;
				}
			}else{
				return defaultReturn;
			}
		},

		/**
		 * @param v
		 * @returns {boolean}
		 */
		isClass: function(v){
			return this.isFn(v) &&
				this.isSet(v.superclass) &&
				this.isSet(v.prototype) &&
				this.isSet(v.__mixins__) &&
				this.isSet(v.__ownClassProperties__) &&
				this.isSet(v.__ownInstanceProperties__) &&
				this.isSet(v.__inherit__);
		},

		/**
		 *
		 * @param v
		 * @returns {boolean}
		 */
		isBool: function(v){
			return v === true || v === false;
		},

		/**
		 * @param v
		 * @returns {*}
		 */
		count: function(v){
			return this.isIterable(v)
				? (v instanceof Array?v.length:Object.keys(v).length)
				: (this.isEmpty(v) ?0:1);
		},

		/**
		 * @param {Function} fn1
		 * @param {Function} fn2
		 * @returns {boolean}
		 */
		compareFn: function(fn1,fn2){
			var re = /[\s]+/g;
			return (""+fn1+"").replace(re,'') === (""+fn2+"").replace(re,'');
		},

		clone: function(data,safeDescriptors){
			var p,descriptor;
			if(this.isData(data)){
				var o = {};
				for(p in data){
					if(safeDescriptors){
						descriptor = Object.getOwnPropertyDescriptor(data,p);
						if(descriptor.hasOwnProperty('value')){
							descriptor.value = this.clone(descriptor.value,safeDescriptors);
						}
						Object.defineProperty(o,p,descriptor);
					}else{
						o[p] = this.clone(data[p],safeDescriptors);
					}
				}
				return o;
			}else if(this.isArray(data)){
				var a = [];
				for(var i =0;i< data.length;i++){
					a.push(this.clone(data[i],safeDescriptors));
				}
				return a;
			}else{
				return data;
			}
		},

		inArray: function(array,needle,strict){
			for(var i = 0; i < array.length;i++){
				if((strict && array[i] === needle) || (!strict && array[i] == needle)){
					return true;
				}
			}
		},

		/**
		 *
		 * @param target
		 * @param source
		 * @returns {*}
		 * @param safeDescriptors
		 */
		apply: function(target,source,safeDescriptors){
			var p ;

			if(safeDescriptors){
				for(p in source){
					Object.defineProperty(target,p,Object.getOwnPropertyDescriptor(source,p));
				}
			}else{
				for(p in source){
					target[p] = source[p];
				}
			}

			return target;
		},

		/**
		 *
		 * @param target
		 * @param source
		 * @param safeDescriptors
		 */
		applyIf: function(target,source,safeDescriptors){
			var p;
			if(safeDescriptors){
				for(p in source){
					if(!target.hasOwnProperty(p)){
						Object.defineProperty(target,p,Object.getOwnPropertyDescriptor(source,p));
					}
				}
			}else{
				for(p in source){
					if(!target.hasOwnProperty(p)){
						target[p] = source[p];
					}
				}
			}
			return target;
		},

		/**
		 * @param iterable
		 * @param callable
		 */
		foreach: function(iterable,callable){
			for(var p in iterable){
				this.callCallable(callable,null,p,iterable[p],iterable);
			}
		},

		/**
		 *
		 * @param {Object} object
		 * @param checker
		 * @param ckecked
		 * @param descriptor
		 * @returns {{}}
		 */
		collect: function(object,checker,ckecked,descriptor){
			var o = {};
			if(this.isObject(object)){
				for(var p in object){
					if(object.hasOwnProperty(p) && this.callCallable(checker,true,[object[p],p,object,o])){
						var desc = Object.getOwnPropertyDescriptor(object,p);
						Object.defineProperty(o,p,this.isCallable(descriptor)?this.callCallable(descriptor,desc,[object[p],p,desc,object,o]):desc);
						if(ckecked)this.callCallable(ckecked,true,[object[p],p,object,o]);
					}
				}
			}
			return o;
		},

		/**
		 * @OOP-Base-Class
		 */
		Base: (function(){
			var that = this;
			var Base = function(){};
			Object.defineProperties(Base,{
				superclass:{value: Object},
				__inherit__:{value: false,writable: true},
				__defaults__:{value: {}},
				__mixins__:{value: []},
				__ownInstanceProperties__: {value: {}},
				__ownClassProperties__:{value: {}},

				callParent:{
					value: function(args){
						if(arguments.callee && arguments.callee.caller && typeof arguments.callee.caller.__parent_method__ !== "undefined"){
							return arguments.callee.caller.__parent_method__.apply(this, args || []);
						}return undefined;
					}
				}
			});
			Base.prototype = {};
			Object.defineProperties(Base.prototype,{

				constructor: {
					value:emptyFn
				},

				callExtend: {
					value: function(config,definition){
						if(!definition){
							definition = config;
						}
						config = that.apply(config || {},{
							parent: this.__class__
						});
						return that.define(config,definition);
					}
				},

				callParent: {
					value: function(args){
						if(arguments.callee && arguments.callee.caller){
							if(typeof arguments.callee.caller.__tmp_parent__ !== "undefined"){
								return arguments.callee.caller.__tmp_parent__.apply(this, args || []);
							}else if(typeof arguments.callee.caller.__parent_method__ !== "undefined"){
								return arguments.callee.caller.__parent_method__.apply(this, args || []);
							}else{
								console.log([arguments.callee.caller],'caller is not parent method');
							}

						}return undefined;
					}
				},

				callMixinConstructor: {
					value: function(args,mixin){
						for(var i =0;i< this.__class__.mixins.length;i++){
							var m = this.__class__.mixins[i];
							if(!mixin || m===mixin){
								m.prototype.constructor.apply(this,args||[]);
							}
						}
					}
				},

				getDefaultProperty: {
					value: function(key){
						if(typeof this.__class__.__allDefaultInstanceProperties__[key] !== "undefined"){
							return this.__class__.__allDefaultInstanceProperties__[key];
						}
						return undefined;
					}
				}
			});
			return Base;
		})(),

		setSafeInstanceProperty: function(instance,key,value){
			if(this.isInstance(instance) && instance.hasOwnProperty(key)){
				Object.defineProperty(instance,key, this.apply(Object.getOwnPropertyDescriptor(instance,key),{
					value: value
				}));
			}
		},

		hasMixinInClass: function(definedClass,mixin){
			if(this.isClass(definedClass) && definedClass.__mixins__){
				for(var i=0;i< definedClass.__mixins__.length;i++){
					if(definedClass.__mixins__[i] === mixin){
						return true;
					}
				}
				return definedClass.superclass?this.hasMixinInClass(definedClass.superclass,mixin):false;
			}
			return false;
		},

		collectClassDefaultProperties: function(cls){
			var defaults;
			if(this.isClass(cls)){
				defaults = {};
				var that = this,
					superclassProperties = this.collectClassDefaultProperties(cls.superclass),
					properties = cls.__ownInstanceProperties__,
					mixinsProperties = (function(){
						var o = {};
						for(var i=0;i < cls.__mixins__.length;i++){
							that.apply(o,that.collectClassDefaultProperties(cls.__mixins__[i])||{},true);
						}
						return o;
					})();

				this.apply(defaults,superclassProperties,true);
				this.apply(defaults,mixinsProperties,true);
				this.apply(defaults,properties,true);
			}
			return defaults?this.clone(defaults):{};
		},

		isClassSystemProperty: function(p){
			return this.inArray([
				'superclass',
				'prototype',
				'__ownMixins__',
				'__ownClassProperties__',
				'__ownInstanceProperties__',
				'__allDefaultInstanceProperties__',
				'__inherit__',
				'callParent'
			],p,true);
		},

		isInstanceSystemProperty: function(p){
			return this.inArray([
				'__class__',
				'__parent__',
				'callParent'
			],p,true);
		},

		/**
		 * @param cfg
		 * @param definition
		 * @returns {Function}
		 */
		define: function define(cfg,definition){

			var that = this,
				i,
				config = {
					parent: that.Base,
					static: {},
					mixins: [],
					private: null,
					name: null
				};
			if(this.isSet(definition)){
				if(this.isClass(cfg)){
					config.parent = cfg;
				}else if(this.isData(cfg)){
					this.apply(config,cfg,true);
				}
			}else definition = cfg;

			if(!this.isClass(config.parent)){
				throw Error('is not class passed for parent');
			}
			if(!this.isArray(config.mixins)){
				if(this.isClass(config.mixins)){
					config.mixins = [config.mixins];
				}else{
					throw Error('mixins invalid');
				}

			}
			if(!this.isData(config.static)){
				throw Error('static declaration is invalid');
			}

			var F = function(){
				that.applyIf(this,that.clone(F.__ownInstanceProperties__,true),true);
				F.superclass.__inherit__ = true;
				F.superclass.apply(this,arguments);
				for(var i =0;i< F.__mixins__.length;i++){
					var m = F.__mixins__[i];
					m.__inherit__ = true;
					m.call(this,arguments);
					m.__inherit__ = false;
				}
				F.superclass.__inherit__ = false;
				if(!F.__inherit__){
					this.constructor.apply(this,arguments);
					//F.prototype.constructor.apply(this,arguments);
				}
			};

			Object.defineProperty(F,'superclass',{
				value: config.parent || that.Base
			});

			Object.defineProperty(F,'__mixins__',{
				value:(function(){
					var o = [];
					if(config.mixins){
						if(!that.isArray(config.mixins)){
							config.mixins = [config.mixins];
						}
						for(i=0;i<config.mixins.length;i++){
							if(that.isClass(config.mixins[i]) && !that.hasMixinInClass(F.superclass,config.mixins[i])){
								o.push(config.mixins[i]);
							}
						}
					}
					return o;
				})()
			});

			Object.defineProperty(F,'__inherit__',{
				value:false, writable: true
			});



			/** Instance properties */
			Object.defineProperty(F,'__ownInstanceProperties__',{
				value: that.clone(that.collect(definition,function(value,p){
					return !that.isFn(value) && !that.isInstanceSystemProperty(p);
				}),true)
			});

			/** Instance methods */
			F.prototype = (function(){
				var proto = Object.create(F.superclass.prototype);
				/** instance mixins */
				for(i=0;i< F.__mixins__.length;i++){
					var mixin = F.__mixins__[i].prototype;
					that.apply(
						proto,
						that.collect(mixin, function(val,p){return that.isFn(val) && p !== 'constructor';}),
						true
					);
				}
				/** instance definition */
				var current = that.collect(definition,function(val){
					return that.isFn(val)
				},function(val,p,from,to){
					Object.defineProperty(to[p],'__parent_method__', {value: proto[p] || undefined});
				});

				that.apply(proto,current,true);
				Object.defineProperty(proto,'__class__', {value: F});
				Object.defineProperty(proto,'__parent__', {value: F.superclass});
				return proto;
			})();

			/** Instance defaults */
			Object.defineProperty(F,'__allDefaultInstanceProperties__',{
				value: that.collectClassDefaultProperties(F)
			});



			/** Class properties */
			Object.defineProperty(F,'__ownClassProperties__',{
				value: that.clone(that.collect(config.static || {},function(value,p){
					return !that.isClassSystemProperty(p) && !that.isFn(value)
				}),true)
			});

			/** Class methods */
			var intermediateClass = function(){};
			intermediateClass.__proto__ = F.superclass;
			/** Class mixins methods */
			for(i=0;i< F.__mixins__.length;i++){
				var mixin = F.__mixins__[i].__proto__;
				if(mixin)that.apply(
					intermediateClass,
					that.collect(mixin,function(val,p){
						return !that.isClassSystemProperty(p) && that.isFn(val);
					},null), true
				);
			}
			/** Class definition methods */
			that.apply(intermediateClass,that.collect(config.static || {},function(v,p){
				return !that.isClassSystemProperty(p) && that.isFn(v);
			},function(val,p,from,to){
				Object.defineProperty(to[p],'__parent_method__', {value: F.superclass[p] || undefined});
			}),true);

			F.__proto__ = intermediateClass;

			function constructClass(cls,applyTo){
				that.applyIf(applyTo || cls,cls.__ownClassProperties__,true);
				if(cls.superclass){
					constructClass(cls.superclass,cls);
				}
			}
			constructClass(F);
			return F;
		},

		get test(){
			if(!tester){
				var that = this;
				var values = [
					1,
					0,
					-1,
					100.22,
					100.98,
					100.5,
					NaN,
					'1',
					'0',
					'-1',
					'20px',
					'20.345px',
					'-20.456%',
					'string 20',
					'',
					null,
					function(){},
					[],
					[1],
					[2,3,4],
					{},
					{"key":"value"},
					undefined
				];

				var _checkerNames = null;
				var _converterNames = null;
				tester = {



					get checkerNames(){
						if(!_checkerNames){
							_checkerNames = [];
							for(var p in that){
								if(p.substr(0,2)==='is'){
									_checkerNames.push(p);
								}
							}
						}
						return _checkerNames;
					},

					get converterNames(){
						if(!_converterNames){
							_converterNames = [];
							for(var p in that){
								if(p.substr(0,2)==='to'){
									_converterNames.push(p);
								}
							}
						}
						return _converterNames;
					},

					checkersTest: function(){
						for(var p in that){
							if(p.substr(0,2)==='is'){
								console.log(''+p+' testing');
								for(var vI in values){
									var v = values[vI];
									console.log(v,that[p].call(that,v));
								}
								console.log('');
							}
						}
					},

					convertersTest: function(){
						for(var p in that){
							if(p.substr(0,2)==='to'){
								console.log(''+p+' testing');
								for(var vI in values){
									console.log(values[vI],that[p].call(that,values[vI]));
								}
								console.log('');
							}
						}
					},


					createDumpTo: function(el,dataKey){
						var data = this[dataKey];
						var elements = [];
						el.innerText = '';
						var ct = document.createElement('DIV');
						ct.style.border = '1px solid #333';
						ct.style.padding = '20px';
						ct.style.backgroundColor = 'hsla(0,30%,30%,0.3)';
						ct.style.boxShadow = '1px 1px hsla(0,30%,30%,0.02)';
						ct.innerText = 'please Wait!';
						el.appendChild(ct);
						var __i = 0;
						var cleanup = false;
						var intervalID = setInterval(function(){
							if(!cleanup){
								ct.innerText = '';
								cleanup = true;
							}
							var val = data[__i];
							var el = document.createElement('DIV');
							el.innerText = val;
							elements.push(el);
							ct.appendChild(el);
							__i++;
							if(!Jungle.isSet(data[__i])){
								clearInterval(intervalID);
							}
						},1000);
					}
				};
			}
			return tester;
		}

	};


})();
/*
 Jungle.test.convertersTest();
 Jungle.test.createDumpTo(document.body,'checkerNames');
 */

var Car = Jungle.define(null,(function(){

	var static_private_property = 0;

	return {

		id: 0,

		name: null,

		number: 0,

		container: [],

		registry: {},

		constructor: function(name){
			this.id = ++static_private_property;
			if(name){
				this.name = name;
			}
		},

		getNum1: function(){
			return 1;
		}
	}

})());
var BMW = Jungle.define(Car,(function(){

	var static = 1;

	return {

		name: 'Only BMW',

		constructor: function(){
			this.callParent(arguments);
			this.number++;

			this.container = this.container.concat([this.id,this.name]);
			this.registry[this.id] = this.name;
		},

		get static(){
			return static;
		},

		set static(v){
			static = v;
		}

	};

})());

var Ferrari = Jungle.define(BMW,{

	name: 'Ferrari',

	constructor: function(){
		this.callParent(arguments);
	},

	getNum1: function(){
		this.number = this.callParent(arguments) + 3;
		return this.number;
	}

});

var Turbo = Jungle.define({

	turbo_type: 'aqua',

	turbo_bust: 0.5,

	turbo_filter: true,

	getTurboBust: function(){
		return this.turbo_bust;
	}
});

var Honda = Jungle.define({

	parent: BMW,
	mixins: Turbo,

	static: {

		hondaKey: 'hondaValue',

		getStaticValue: function(){
			return this.hondaKey;
		}

	}

},{

	name: 'Honda',

	constructor: function(){
		this.callParent(arguments);
	},

	getNum1: function(){
		this.number = this.callParent(arguments) + 3;
		return this.number;
	},

	getTurboBust: function(){
		return this.callParent() + 0.5;
	}

});


var Skyline = Jungle.define({
	parent: Honda,
	static: {

		getStaticValue: function(){
			return this.callParent() + ' + Skyline';
		}

	}
},{

	name: 'Nissan Skyline GTR'

});

console.log([Skyline],Skyline.getStaticValue());

var cars = {
	subaru: new Car('Subaru'),
	kia: new Car('KIA'),
	mercedes: new Car('Mercedes'),
	bmw: new BMW(),
	ferrari: new Ferrari(),
	honda: new Honda('Honda'),
	skyline: new Skyline()
};
for(var p in cars){
	if(cars.hasOwnProperty(p)){
		console.log(p,cars[p],Jungle.isInstance(cars[p],Turbo));
	}
}



console.time('test');
var Container = Jungle.define({

	items: [],

	constructor: function(){

	},

	searchItemByKey: function(k){
		for(var i = 0;i < this.items.length;i++){
			if(this.items[i].key === k){
				return this.items[i];
			}
		}
		return null;
	},

	addItem: function(item){
		if(Jungle.isInstance(item,Item)){
			if(this.items.indexOf(item)===-1){
				this.items.push(item);
				item.setContainer(this);
			}
		}
		return this;
	},

	removeItem: function(item){
		var i = this.items.indexOf(item);
		if(i!==-1){
			this.items.splice(i,1);
			if(item.container){
				item.setContainer();
			}
		}
		return this;
	}

});

var Item = Jungle.define({

	container: null,

	key: '',

	setContainer: function(container){
		container = container||null;
		if(!container || Jungle.isInstance(container,Container)){
			var old = this.container;
			if(old !== container){
				this.container = container;
				if(container){
					container.addItem(this);
				}
				if(old){
					old.removeItem(this);
				}
			}
		}
		return this;
	},

	setKey: function(v){
		this.key = v;
		return this;
	},

	toString: function(){
		return this.key;
	}

});

var Item1 = Jungle.define({
	parent: Item
},{

	constructor: function item1Constructor(){
		this.callParent();
	}

});

var ct1 = new Container;

ct1.addItem((new Item).setKey('1'));
ct1.addItem((new Item).setKey('2'));
ct1.addItem((new Item).setKey('3'));
ct1.addItem((new Item).setKey('4'));
ct1.addItem((new Item).setKey('5'));
ct1.addItem((new Item1).setKey('6'));

for(var i =0;i<ct1.items.length;i++){
	var n = document.createElement('h4');
	n.innerText = Jungle.toString(ct1.items[i]);
	document.body.appendChild(n);
}
console.timeEnd('test');