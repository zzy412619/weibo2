
<!-- 全局通用的局部视图，用于展示用户的头像和用户名等基本信息 -->
<a href="{{ route('users.show',$user->id) }}">
	<img src="{{ $user->gravatar('120') }}" alt="{{ $user->name }}">
</a>
<h1>{{ $user->name }}</h1>