<table>
    <tr>
        <th style="text-align: center"><strong>#ID</strong></th>
        <th><strong>Namespace</strong></th>
        <th><strong>Directions</strong></th>
        @foreach ($period as $date)
            <td style="text-align: center" >{{ $dates[] = $date->format('d.m') }}</td>
        @endforeach
    </tr>
	
    @foreach ($attendances as $e)
        <tr>
            <td>{{ $e->id }}</td>
            <td>{{ $e->name }} {{ $e->lastname }}</td>
            <td>{{ $e->directions }}</td>
         	 
            	@foreach ($e->attendances as $attendance)
          		
          			@if($attendance['data'] == $dataNow)
                		<td>{{ $attendance['attendance'] }}</td>
          			@endif
            	@endforeach
         	 
        </tr>
    @endforeach
</table>

