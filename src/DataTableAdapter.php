<?php namespace Mondovo\DataTable;

/**
 * Created by PhpStorm.
 * User: maximizer
 * Date: 14/5/15
 * Time: 1:31 PM
 */
use Mondovo\DataTable\Contracts\DataTableAdapterInterface;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class DataTableAdapter implements DataTableAdapterInterface {

    /**
     * datatable object instance
     *
     * @var object
     */
     private $instance;

    /**
     * datatable querybuilder Object
     *
     * @var object
     */
    protected $query;

    /**
     * All Request Variable in an Array
     *
     * @var array
     */
    protected $input;

    /**
     * All Collecton an Array
     *
     * @var array
     */
    protected $collection;

    /**
     * Query type
     *
     * @var string
     */
    public $query_type;

    /**
     * Columns of Query/collections
     *
     * @var array
     */
    public $columns;

	public $special_filter = false;

	public $disable_cache = false;

    /**
     * Gets query and returns instance of class
     *
     * @param $builder
     * @return object
     */
    public function of($builder)
    {
	    if (!$this->disable_cache && !$this->special_filter && $builder instanceof QueryBuilder) {
	    	$params = implode('||',$builder->getBindings());
		    $cache_key = md5($builder->toSql().$params);
		    if(\Cache::has($cache_key)){
		    	$builder = \Cache::get($cache_key);
		    }else{
			    $builder = $builder->get();
			    set_cache($cache_key, $builder, 240);
		    }
	    }

	    $this->instance = Datatables::of( $builder )->escapeColumns( [] );
	    /* All the Get/ Post Parameters from datatable  in client/browser site */
	    $input = $this->instance->request->all();
	    $this->input = &$input;
	    /* Query Type: Eloquent, QueryBuilder, Collection */
	    if ($builder instanceof QueryBuilder) {
		    $this->query_type = 'builder';
		    $this->columns    = $builder->columns;
		    $this->query      = $this->instance->getQuery();
	    } else if ($builder instanceof Collection) {
		    $this->query_type = 'collection';
		    $this->columns = array_keys($this->serialize($builder->first()));
		    $this->collection = &$this->instance->collection;
	    }else{
		    $this->query = $builder instanceof Builder ? $builder : $builder->getQuery();
		    $this->query_type = 'eloquent';
		    //ToDO: Not tested, Need to add column logic., Currently we are not using it in our project.
	    }
    }

	/**
	 * Serialize collection
	 *
	 * @param  mixed $collection
	 * @return mixed|null
	 */
	protected function serialize($collection)
	{
		return $collection instanceof Arrayable ? $collection->toArray() : (array) $collection;
	}

    /**
     * @return bool
     */
    protected function isCollection()
    {
        return  $this->query_type == 'collection';
    }

    /**
     * Organizes works for creating datatable json output
     *
     * @param bool $mDataSupport
     * @return string
     */
    public function make($mDataSupport = false)
    {
	    $this->instance->request->merge( $this->input );
        return $this->instance->make($mDataSupport);
    }

    /**
     * Edit column's content
     *
     * @param  string $name
     * @param  string $content
     * @return Datatables
     */
    public function editColumn($name, $content)
    {
        $this->instance->editColumn($name, $content);
        return  $this;
    }

    /**
     * Add column in collection
     *
     * @param string $name
     * @param string $content
     * @param bool|int $order
     * @return Datatables
     */
    public function addColumn($name, $content, $order = false)
    {
        $this->instance->addColumn($name, $content, $order);
        return  $this;
    }

    /**
     * Override default column filter search
     *
     * @param mixed ...,... All the individual parameters required for specified $method
     * @return $this
     */
    public function filterColumn()
    {
        $params = func_get_args();
        call_user_func_array(array($this->instance, "filterColumn"), $params);
        return $this;
    }

}