@csrf
@if($errors->any())<div class="error" role="alert"><p>Please correct the highlighted fields.</p></div>@endif
<label for="title">Title</label><input id="title" name="title" maxlength="150" required value="{{ old('title', $task->title) }}">@error('title')<p class="error">{{ $message }}</p>@enderror
<label for="description">Description</label><textarea id="description" name="description" maxlength="5000">{{ old('description', $task->description) }}</textarea>@error('description')<p class="error">{{ $message }}</p>@enderror
<label for="due_date">Due date</label><input id="due_date" type="date" name="due_date" required value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">@error('due_date')<p class="error">{{ $message }}</p>@enderror
<label for="priority">Priority</label><select id="priority" name="priority">@foreach(['low','medium','high'] as $value)<option value="{{ $value }}" @selected(old('priority', $task->priority) === $value)>{{ ucfirst($value) }}</option>@endforeach</select>@error('priority')<p class="error">{{ $message }}</p>@enderror
<label for="status">Status</label><select id="status" name="status">@foreach(['pending','in_progress','done'] as $value)<option value="{{ $value }}" @selected(old('status', $task->status) === $value)>{{ ucfirst(str_replace('_',' ', $value)) }}</option>@endforeach</select>@error('status')<p class="error">{{ $message }}</p>@enderror
<p class="row"><button type="submit">Save task</button><a href="{{ route('tasks.index') }}">Cancel</a></p>
