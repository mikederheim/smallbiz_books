<h1>Journal Entry</h1>
<p><a href="<?=route('journal')?>">&larr; Back to journal entries</a><?php if(($entry['source_type']??'')==='manual_journal'): ?> | <a href="<?=route('journal_edit')?>&id=<?=$entry['id']?>">Edit</a><?php endif; ?></p>
<div class="cards"><div>Date<strong><?=h($entry['entry_date'])?></strong></div><div>Source<strong><?=h($entry['source_type'] ?: 'manual')?></strong></div></div>
<p><b>Memo:</b> <?=h($entry['memo'])?></p>
<table><tr><th>Account</th><th>Description</th><th>Debit</th><th>Credit</th></tr><?php $debits=0;$credits=0;foreach($lines as $l):$debits+=(float)$l['debit'];$credits+=(float)$l['credit'];?><tr><td><?=h($l['code'].' - '.$l['name'])?></td><td><?=h($l['description'])?></td><td>$<?=number_format((float)$l['debit'],2)?></td><td>$<?=number_format((float)$l['credit'],2)?></td></tr><?php endforeach;?><tr><th colspan="2">Totals</th><th>$<?=number_format($debits,2)?></th><th>$<?=number_format($credits,2)?></th></tr></table>
