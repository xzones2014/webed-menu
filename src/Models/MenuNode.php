<?php namespace WebEd\Base\Menu\Models;

use WebEd\Base\Menu\Models\Contracts\MenuNodeModelContract;
use WebEd\Base\Models\EloquentBase as BaseModel;

class MenuNode extends BaseModel implements MenuNodeModelContract
{
    protected $table = 'menu_nodes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'menu_id', 'parent_id', 'related_id', 'type', 'url', 'title', 'icon_font', 'css_class', 'target', 'sort_order',
    ];

    public $timestamps = true;

    protected $relatedModelInfo = [];

    /**
     * @param $value
     * @return mixed|string
     */
    public function getTitleAttribute($value)
    {
        if ($value) {
            return $value;
        }
        if (!$this->resolveRelatedModel()) {
            return '';
        }

        return array_get($this->relatedModelInfo, 'model_title');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getUrlAttribute($value)
    {
        if (!$this->resolveRelatedModel()) {
            return $value;
        }

        return array_get($this->relatedModelInfo, 'url');
    }

    protected function resolveRelatedModel()
    {
        if ($this->type === 'custom-link') {
            return null;
        }
        $this->relatedModelInfo = menus_management()->getObjectInfoByType($this->type, $this->related_id);

        return $this->relatedModelInfo;
    }
}
