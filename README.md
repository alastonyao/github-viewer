# github-viewer

# Command CLI
Clone a new repository :
- php bin/console app:import-repo <gitHub repository Url>
Get all Commits : 
- php bin/console app:display-commits <gitHub repository Url>
  
# API REST
- Clone repository
Method : POST 
URL : /repository/add 
Data JSON : 
{
  bapirest : true,
  url : <url repository>
}
  
- List Repository
 Method : GET
 URL : /repository/list
 
 - Delete Repository
Method : POST
URL : /repository/delete
{
  url : <url repository>
}
