cmd /c "mysql -u root restful_dev < reset.sql"
newman run collection.json -e environment.json