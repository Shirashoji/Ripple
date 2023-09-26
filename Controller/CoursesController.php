<?php
/**
 * iroha Board Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2016 iroha Soft, Inc. (http://irohasoft.jp)
 * @link          http://irohaboard.irohasoft.jp
 * @license       http://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses("AppController", "Controller");

class CoursesController extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $components = [
        "Security" => [
            "csrfUseOnce" => false,
            "unlockedActions" => ["admin_order"],
        ],
    ];

    /**
     * コース一覧を表示
     */
    public function admin_index()
    {
        $this->Course->unbindModel([
            "hasMany" => ["Content", "Record", "ClearedContent"],
        ]);

        $this->loadModel("Category");
        $in_category_courses = $this->Category->find("all", [
            "order" => ["Category.sort_no" => "asc"],
        ]);

        $in_category_courses_list = $this->Category->find("list", [
            "order" => ["Category.sort_no" => "asc"],
        ]);
        $json_list = json_encode($in_category_courses_list);

        $out_category_courses = $this->Course->find("all", [
            "conditions" => [
                "Course.category_id" => null,
            ],
            "order" => ["Course.sort_no" => "asc"],
        ]);

        $courses = $this->Course->find("all", [
            "order" => ["Course.sort_no" => "asc"],
        ]);
        $this->set(
            compact("in_category_courses", "out_category_courses", "json_list")
        );
    }

    /**
     * コースの追加
     */
    public function admin_add()
    {
        $this->admin_edit();
        $this->render("admin_edit");
    }

    /**
     * コースの編集
     * @param int $course_id コースID
     */
    public function admin_edit($course_id = null)
    {
        $this->loadModel("Category");

        $category_list = $this->Category->find("list", [
            "fields" => ["Category.id", "Category.title"],
            "order" => ["Category.sort_no" => "asc"],
        ]);
        $this->set(compact("category_list"));

        if ($this->action == "edit" && !$this->Course->exists($course_id)) {
            throw new NotFoundException(__("Invalid course"));
        }
        $course_list = $this->Course->getCourseList();
        $this->set("course_list", $course_list);

        if ($course_id != null) {
            $courseInfo = $this->Course->getCourseInfo($course_id);
            $selected_before_course = $courseInfo["Course"]["before_course"];
            //$this->log($courseInfo);
            $this->set("selected_before_course", $selected_before_course);
        }
        if ($this->request->is(["post", "put"])) {
            if (Configure::read("demo_mode")) {
                return;
            }

            // 作成者を設定
            $this->request->data["Course"]["user_id"] = $this->Auth->user("id");
            $this->log($this->request->data);

            if ($this->Course->save($this->request->data)) {
                $this->Flash->success(__("コースが保存されました"));
                return $this->redirect([
                    "action" => "index",
                ]);
            } else {
                $this->Flash->error(
                    __("The course could not be saved. Please, try again.")
                );
            }
        } else {
            $options = [
                "conditions" => [
                    "Course." . $this->Course->primaryKey => $course_id,
                ],
            ];
            $this->request->data = $this->Course->find("first", $options);
        }
    }

    /**
     * コースの削除
     * @param int $course_id コースID
     */
    public function admin_delete($course_id = null)
    {
        if (Configure::read("demo_mode")) {
            return;
        }

        $this->Course->id = $course_id;
        if (!$this->Course->exists()) {
            throw new NotFoundException(__("Invalid course"));
        }

        $this->request->allowMethod("post", "delete");
        $this->Course->deleteCourse($course_id);
        $this->Flash->success(__("コースが削除されました"));

        return $this->redirect([
            "action" => "index",
        ]);
    }

    /**
     * Ajax によるコースの並び替え
     *
     * @return string 実行結果
     */
    public function admin_order()
    {
        $this->autoRender = false;
        if ($this->request->is("ajax")) {
            $this->Course->setOrder($this->data["id_list"]);
            return "OK";
        }
    }
}
