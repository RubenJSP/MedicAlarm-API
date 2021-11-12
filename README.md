# MedicAlarm-API
API para el proyecto MedicAlarm de la materia Desarrollo de un Proyecto de Software
<p align="center"><img src="https://github.com/ignacioiglesias43/MedicAlarmv2/blob/dev/src/assets/logo.png?raw=true" width="150"></p>
## How To Run ##
Esta aplicacion requiere Node y PHP Composer

En la en la raiz del proyecto
```
composer update
```
```
npm install
```
```
php artisan key:generate
```
```
php artisan migrate:fresh --seed
```
```
npm run dev
```
Los siguientes comandos se ejecutan por separado:
```
php artisan serve 
```

```
php artisan schedule:work 
```

