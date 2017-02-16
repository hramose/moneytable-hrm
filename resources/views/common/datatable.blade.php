
					
					<div class="table-responsive">
						<table class="table table-hover datatable" data-table-source="{{ $table_info['source'] }}" data-table-title="{{ $table_info['title'] }}" id="{{ $table_info['id'] }}" {!! array_key_exists('form',$table_info) ? 'data-form="'.$table_info['form'].'"' : '' !!}>
							<thead>
								<tr>
									@foreach($col_heads as $col_head)
									@if($col_head == 'Option')
									<th style="min-width:100px;">{!! $col_head !!}</th>
									@else
									<th>{{ $col_head }}</th>
									@endif
									@endforeach
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>