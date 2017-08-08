PHP5添加了一项新的功能：Reflection。这个功能使得程序员可以reverse-engineer class, interface,function,method and extension。通过PHP代码，就可以得到某object的所有信息，并且可以和它交互。
假设有一个类Person：
class Person {  
	/** 
     * For the sake of demonstration, we"re setting this private
     */ 
    private $_allowDynamicAttributes = false;
 
    /** type=primary_autoincrement */
    protected $id = 0;
 
    /** type=varchar length=255 null */
    protected $name;
 
    /** type=text null */
    protected $biography;
 
        public function getId()
        {
        	return $this->id;
        }
        public function setId($v)
        {
           	$this->id = $v;
        }
        public function getName()
        {
       		return $this->name;
        }
        public function setName($v)
        {
         	$this->name = $v;
        }
        public function getBiography()
        {
          	return $this->biography;
        }
        public function setBiography($v)
        {
         	$this->biography = $v;
        }
}

通过ReflectionClass，我们可以得到Person类的以下信息：
常量 Contants
属性 Property Names
方法 Method Names
静态属性 Static Properties
命名空间 Namespace
Person类是否为final或者abstract
只要把类名"Person"传递给ReflectionClass就可以了：
$class = new ReflectionClass('Person');

获取属性(Properties)：

$properties = $class->getProperties();
foreach($properties as $property) {
    echo $property->getName()."\n";
}
// 输出:
// _allowDynamicAttributes
// id
// name
// biography


默认情况下，ReflectionClass会获取到所有的属性，private 和 protected的也可以。如果只想获取到private属性，就要额外传个参数：


$private_properties = $class->getProperties(ReflectionProperty::IS_PRIVATE);


可用参数列表：
ReflectionProperty::IS_STATIC
ReflectionProperty::IS_PUBLIC
ReflectionProperty::IS_PROTECTED
ReflectionProperty::IS_PRIVATE
如果要同时获取public 和private 属性，就这样写：ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
应该不会感觉陌生吧。
通过$property->getName()可以得到属性名，通过getDocComment可以得到写给property的注释。


foreach($properties as $property) {
    if($property->isProtected()) {
        $docblock = $property->getDocComment();
        preg_match('/ type\=([a-z_]*) /', $property->getDocComment(), $matches);
        echo $matches[1]."\n";
    }
}
// Output:
// primary_autoincrement
// varchar
// text

有点不可思议了吧。竟然连注释都可以取到。
获取方法(methods)：通过getMethods() 来获取到类的所有methods。返回的是ReflectionMethod对象的数组。不再演示。
最后通过ReflectionMethod来调用类里面的method。

$data = array("id" => 1, "name" => "Chris", "biography" => "I am am a PHP developer");
foreach($data as $key => $value) {
    if(!$class->hasProperty($key)) {
        throw new Exception($key." is not a valid property");
    }
 
    if(!$class->hasMethod("get".ucfirst($key))) {
        throw new Exception($key." is missing a getter");
    }
 
    if(!$class->hasMethod("set".ucfirst($key))) {
        throw new Exception($key." is missing a setter");
    }
 
    // Make a new object to interact with
    $object = new Person();
 
    // Get the getter method and invoke it with the value in our data array
    $setter = $class->getMethod("set".ucfirst($key));
    $ok = $setter->invoke($object, $value);
 
    // Get the setter method and invoke it
    $setter = $class->getMethod("get".ucfirst($key));
    $objValue = $setter->invoke($object);
 
    // Now compare
    if($value == $objValue) {
        echo "Getter or Setter has modified the data.\n";
    } else {
        echo "Getter and Setter does not modify the data.\n";
   }
}



