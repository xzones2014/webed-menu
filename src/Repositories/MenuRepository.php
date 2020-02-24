<?php namespace WebEd\Base\Menu\Repositories;

use WebEd\Base\Caching\Services\Traits\Cacheable;
use WebEd\Base\Menu\Models\Menu;
use WebEd\Base\Models\Contracts\BaseModelContract;
use WebEd\Base\Repositories\Eloquent\EloquentBaseRepository;
use WebEd\Base\Caching\Services\Contracts\CacheableContract;
use WebEd\Base\Menu\Repositories\Contracts\MenuNodeRepositoryContract;
use WebEd\Base\Menu\Repositories\Contracts\MenuRepositoryContract;

class MenuRepository extends EloquentBaseRepository implements MenuRepositoryContract, CacheableContract
{
    use Cacheable;

    /**
     * @var MenuNodeRepository|MenuNodeRepositoryCacheDecorator
     */
    protected $menuNodeRepository;

    public function __construct(BaseModelContract $model)
    {
        parent::__construct($model);

        $this->menuNodeRepository = app(MenuNodeRepositoryContract::class);
    }

    /**
     * @param array $data
     * @param array|null $menuStructure
     * @return int|null
     */
    public function createMenu(array $data, array $menuStructure = null)
    {
        $result = $this->create($data);
        if (!$result || !$menuStructure) {
            return $result;
        }
        if ($menuStructure !== null) {
            $this->updateMenuStructure($result, $menuStructure);
        }

        return $result;
    }

    /**
     * @param Menu|int $id
     * @param array $data
     * @param array|null $menuStructure
     * @param array|null $deletedNodes
     * @return int|null
     */
    public function updateMenu($id, array $data, array $menuStructure = null, array $deletedNodes = null)
    {
        $result = $this->update($id, $data);

        if (!$result || !$menuStructure) {
            return $result;
        }

        if($deletedNodes) {
            $this->menuNodeRepository->delete($deletedNodes);
        }

        if ($menuStructure !== null) {
            $this->updateMenuStructure($result, $menuStructure);
        }

        return $result;
    }

    /**
     * @param int $menuId
     * @param array $menuStructure
     */
    public function updateMenuStructure($menuId, array $menuStructure)
    {
        foreach ($menuStructure as $order => $node) {
            $this->menuNodeRepository->updateMenuNode($menuId, $node, $order);
        }
    }

    /**
     * @param Menu|int $id
     * @return \Illuminate\Database\Eloquent\Builder|null|Menu|\WebEd\Base\Models\EloquentBase
     */
    public function getMenu($id)
    {
        if($id instanceof Menu) {
            $menu = $id;
        } else {
            $menu = $this->find($id);
        }
        if(!$menu) {
            return null;
        }

        $menu->all_menu_nodes = $this->menuNodeRepository->getMenuNodes($menu);

        return $menu;
    }
}
