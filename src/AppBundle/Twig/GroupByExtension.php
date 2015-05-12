<?php

namespace AppBundle\Twig;

class GroupByExtension extends \Twig_Extension
{

    static protected $cache = array();

    /** @var \Twig_Environment env */
    protected $env;

    public function initRuntime(\Twig_Environment $environment) {
        $this->env = $environment;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'groupby';
    }


    public function getFilters() {
        return array(
            'groupby'  => new \Twig_Filter_Method($this, 'groupBy'),
        );
    }


    /**
     * @param $collection
     * @param $group_attr
     * @param string $format
     * @param null $timezone
     * @return array
     */
    public function groupBy($collection, $group_attr, $format = 'd.m.Y h:m:s', $timezone = null) {

        $grouped_collection = array();

        foreach ($collection as $entity) {

            $key = 'Unknown';

            if($attr = $this->getAttribute($entity, $group_attr)) {
                $key = $attr;
                if($attr instanceof \DateTime) {
                    $key = twig_date_format_filter($attr, $format, $timezone);
                }
            }

            if(isset($grouped_collection[$key]) === false) {
                $grouped_collection[$key] = array(
                    'grouper' => $key,
                    'list' => array()
                );
            }

            $grouped_collection[$key]['list'][] = $entity;
        }

        return $grouped_collection;
    }



    /**
     * Returns the attribute value for a given array/object.
     *
     * @param mixed   $object        The object or array from where to get the item
     * @param mixed   $item          The item to get from the array or object
     * @param array   $arguments     An array of arguments to pass if the item is an object method
     * @param string  $type          The type of attribute (@see \Twig_Template)
     * @param Boolean $isDefinedTest Whether this is only a defined check
     * @return
     *
     * @throws Twig_Error_Runtime
     */
    protected function getAttribute($object, $item, array $arguments = array(), $type = \Twig_Template::ANY_CALL, $isDefinedTest = false)
    {
        // array
        if (\Twig_Template::METHOD_CALL !== $type) {
            if ((is_array($object) && array_key_exists($item, $object))
                || ($object instanceof \ArrayAccess && isset($object[$item]))
            ) {
                if ($isDefinedTest) {
                    return true;
                }

                return $object[$item];
            }

            if (\Twig_Template::ARRAY_CALL === $type) {
                if ($isDefinedTest) {
                    return false;
                }

                if (!$this->env->isStrictVariables()) {
                    return null;
                }

                if (is_object($object)) {
                    throw new \Twig_Error_Runtime(sprintf('Key "%s" in object (with ArrayAccess) of type "%s" does not exist', $item, get_class($object)));
                    // array
                } else {
                    throw new \Twig_Error_Runtime(sprintf('Key "%s" for array with keys "%s" does not exist', $item, implode(', ', array_keys($object))));
                }
            }
        }

        if (!is_object($object)) {
            if ($isDefinedTest) {
                return false;
            }

            if (!$this->env->isStrictVariables()) {
                return null;
            }

            throw new \Twig_Error_Runtime(sprintf('Item "%s" for "%s" does not exist', $item, $object));
        }

        // get some information about the object
        $class = get_class($object);
        if (!isset(self::$cache[$class])) {
            $r = new \ReflectionClass($class);
            self::$cache[$class] = array('methods' => array(), 'properties' => array());
            foreach ($r->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                self::$cache[$class]['methods'][strtolower($method->getName())] = true;
            }

            foreach ($r->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                self::$cache[$class]['properties'][$property->getName()] = true;
            }
        }

        // object property
        if (\Twig_Template::METHOD_CALL !== $type) {
            if (isset(self::$cache[$class]['properties'][$item])
                || isset($object->$item) || array_key_exists($item, $object)
            ) {
                if ($isDefinedTest) {
                    return true;
                }

                if ($this->env->hasExtension('sandbox')) {
                    $this->env->getExtension('sandbox')->checkPropertyAllowed($object, $item);
                }

                return $object->$item;
            }
        }

        // object method
        $lcItem = strtolower($item);
        if (isset(self::$cache[$class]['methods'][$lcItem])) {
            $method = $item;
        } elseif (isset(self::$cache[$class]['methods']['get'.$lcItem])) {
            $method = 'get'.$item;
        } elseif (isset(self::$cache[$class]['methods']['is'.$lcItem])) {
            $method = 'is'.$item;
        } elseif (isset(self::$cache[$class]['methods']['__call'])) {
            $method = $item;
        } else {
            if ($isDefinedTest) {
                return false;
            }

            if (!$this->env->isStrictVariables()) {
                return null;
            }

            throw new \Twig_Error_Runtime(sprintf('Method "%s" for object "%s" does not exist', $item, get_class($object)));
        }

        if ($isDefinedTest) {
            return true;
        }

        if ($this->env->hasExtension('sandbox')) {
            $this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
        }

        $ret = call_user_func_array(array($object, $method), $arguments);

        if ($object instanceof \Twig_Template) {
            return new \Twig_Markup($ret, 'UTF-8');
        }

        return $ret;
    }
}