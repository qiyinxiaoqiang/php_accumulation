PHP5�����һ���µĹ��ܣ�Reflection���������ʹ�ó���Ա����reverse-engineer class, interface,function,method and extension��ͨ��PHP���룬�Ϳ��Եõ�ĳobject��������Ϣ�����ҿ��Ժ���������
������һ����Person��
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

ͨ��ReflectionClass�����ǿ��Եõ�Person���������Ϣ��
���� Contants
���� Property Names
���� Method Names
��̬���� Static Properties
�����ռ� Namespace
Person���Ƿ�Ϊfinal����abstract
ֻҪ������"Person"���ݸ�ReflectionClass�Ϳ����ˣ�
$class = new ReflectionClass('Person');

��ȡ����(Properties)��

$properties = $class->getProperties();
foreach($properties as $property) {
    echo $property->getName()."\n";
}
// ���:
// _allowDynamicAttributes
// id
// name
// biography


Ĭ������£�ReflectionClass���ȡ�����е����ԣ�private �� protected��Ҳ���ԡ����ֻ���ȡ��private���ԣ���Ҫ���⴫��������


$private_properties = $class->getProperties(ReflectionProperty::IS_PRIVATE);


���ò����б�
ReflectionProperty::IS_STATIC
ReflectionProperty::IS_PUBLIC
ReflectionProperty::IS_PROTECTED
ReflectionProperty::IS_PRIVATE
���Ҫͬʱ��ȡpublic ��private ���ԣ�������д��ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
Ӧ�ò���о�İ���ɡ�
ͨ��$property->getName()���Եõ���������ͨ��getDocComment���Եõ�д��property��ע�͡�


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

�е㲻��˼���˰ɡ���Ȼ��ע�Ͷ�����ȡ����
��ȡ����(methods)��ͨ��getMethods() ����ȡ���������methods�����ص���ReflectionMethod��������顣������ʾ��
���ͨ��ReflectionMethod�������������method��

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



