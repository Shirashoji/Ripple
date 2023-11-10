<?php echo $this->element('admin_menu')?>
<?php echo $this->Html->css('custom');?>
<div class = "admin-dates-index full-view">
  <div class="ib-page-title"><?php echo __('授業日一覧'); ?></div>
  <div class="buttons_container">
		<button type="button" class="btn btn-primary btn-add" onclick="location.href='<?php echo Router::url(array('action' => 'add')) ?>'">+ 追加</button>
	</div>
  <div class="buttons_container">
		<button type="button" class="btn btn-outline-primary btn-add" onclick="location.href='<?php echo Router::url(array('action' => 'add_quadruple','online')) ?>'">+ オンライン授業４回分を追加</button>
		<button type="button" class="btn btn-outline-secondary btn-add" onclick="location.href='<?php echo Router::url(array('action' => 'add_quadruple','normal')) ?>'">+ 通常授業４回分追加</button>
	</div>
  <table>
	  <thead>
	    <tr>
        <th nowrap><?php echo $this->Paginator->sort('date', '授業日'); ?></th>
        <th nowrap><?php echo __('授業形式'); ?></th>
        <th class="ib-col-action"><?php echo __('Actions'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($dates as $date):
        $is_online = $date['Date']['online'] ? 'オンライン授業' : '通常授業';
      ?>
      <tr>
        <td nowrap>
          <?php
            echo $this->Html->link($date['Date']['date'], array('controller' => 'lessons', 'action' => 'index', $date['Date']['id']));
            echo $this->Form->hidden('id', array('id'=>'', 'class'=>'date_id', 'value'=>$date['Date']['id']));
          ?>
        </td>
        <td nowrap><?php echo h($is_online); ?></td>
        <td nowrap class="ib-col-action">
          <?php echo $this->Form->postLink(__('削除'),
            array('action' => 'delete', $date['Date']['id']),
            array('class'=>'btn btn-danger'),
            __('[%s] を削除してもよろしいですか?', $date['Date']['date'])
          ); ?>
          <button type="button" class="btn btn-success" onclick="location.href='<?php echo Router::url(array('action' => 'edit', $date['Date']['id'])) ?>'">編集</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php //echo $this->element('paging'); ?>
	<div class="imitated-paging" style="margin-left: 1em;">
		<ul class="pagination">
			<?php
				$this->Paginator->options(array('class' => 'page-link'));
				echo $this->Paginator->numbers(array('currentTag' => 'a class="page-link"'));
			?>
		</ul>
	</div>
</div>
