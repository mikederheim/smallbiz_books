<h1>Journal Entries</h1>
<p><a class="button" href="<?=route('journal_new')?>">New journal entry</a></p>
<table>
  <tr><th>Date</th><th>Memo</th><th>Source</th><th>Lines</th><th>Total debits</th><th>Total credits</th><th>Actions</th></tr>
  <?php foreach($entries as $e): ?>
    <tr>
      <td><?=h($e['entry_date'])?></td><td><?=h($e['memo'])?></td><td><?=h($e['source_type'] ?: 'manual')?></td><td><?=h($e['line_count'])?></td><td>$<?=number_format((float)$e['total_debits'], 2)?></td><td>$<?=number_format((float)$e['total_credits'], 2)?></td>
      <td><a href="<?=route('journal_view')?>&id=<?=$e['id']?>">View</a><?php if(($e['source_type']??'')==='manual_journal'): ?> | <a href="<?=route('journal_edit')?>&id=<?=$e['id']?>">Edit</a> <form method="post" action="<?=route('journal_delete')?>" class="inline" onsubmit="return confirm('Delete this journal entry?');"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="id" value="<?=$e['id']?>"><button>Delete</button></form><?php endif; ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php if(empty($entries)): ?><p>No journal entries yet.</p><?php endif; ?>
