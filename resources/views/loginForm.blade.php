<form action="login" method="post">
  @csrf
  <input name="email" type="text"><br>
  <input name="passwd" type="password"><br>
  <input type="submit" value="登入">
  <input type ="hidden" name ="_token" value ="{{ csrf_token() }}">
</form>
