<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 14:45
 */

namespace app\api\model;


require_once '../vendor/tmdatapull/GetData.php';

class TmPullDataModel
{

    /*拉取数据
(1) 说明：
每次返回数据限制为 10 篇文章，并且选取符合条件的、id 最小的 10 篇文章返回；暂时
没有提供翻页参数，可以利用 min_id 等参数来实现类似翻页效果；
因 为 为 了 规 避 java 或 mysql 的 关 键 词 ， 有 部 分 返 回 参 数 字 段 修 改 了 名 称 ：
abstract->abstractt, column->column_name

key  用户凭证，在天马平台获取

arr数组中参数名称 参数含义 参数样例
column_ids 分类 id；选择某分类文章 7 or 1,7
from_ids 来源网站 id；选择某分类文章 7 or 1,7
min_id 最小文章 id； 100
max_id 最大文章 id； 1000
min_publish_time 最小发布时间； 2018-10-14
max_publish_time 最大发布时间； 2018-10-12 12:30:30
min_collect_time 最小采集时间； 2018-10-14
max_collect_time 最大采集时间； 2018-10-12 12:30:30

这些参数全部放入一个数组中传入

eg：
$arr = [
'column_ids'=>'1,2' ,//or 'column_ids'=>'1' 分类id，不传代表全部，以英文逗号分隔多个，只能拉取拥有的栏目，
 'from_ids'=>'1,2' ,//or 'from_ids'=>'1' 来源id，不传代表全部，以英文逗号分隔多个，只能拉取拥有的栏目，
 'max_id'=>'1'
 'min_publish_time'=>'2018-10-14'
]

(3) 类型名称和 id 表
    所需要的分类类别：
    1. 热点头条
    2. 科技
    3. 娱乐
    4. 游戏
    5. 体育
    6. 汽车
    7. 财经
    8. 时尚
    9. 搞笑
    10. 旅游
    11. 育儿/母婴
    12. 美食
    13. 美文
    14. 历史
    15. 养生/健康
    16. 国际
    17. 军事
    18. 宠物
    19. 星座
    20. 动漫
*/

    public function getPullData($key = "",$arr = []){
        $GetData = new \GetData();
        $GetData->setKey($key);   //设置key值，key值从平台过来
        return $GetData->pullData($arr);
    }

}