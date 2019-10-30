Ceive.Data.Record (Object Relational Model)
===========================================

Задачи ставящиеся перед пакетом:

 - Active Record or Repository
 - Описание схем
 - Описание полей
 - Описание внешних связей
 - Валидация
 - Транзакции
 - Контроль состояния объекта / Snapshot capturing
 - Связанная валидация (Relation Validation)
 - Комплексная операция (for relation dirty control)
 - Поля текстового поиска по умолчанию
 - Наследование условий
 - "Поле запуска" - Наследованные поведения схем.
 - "Динамический ключ" - Polymorphic Association
 - Migration Control*
 - Fixtures*
 - Относительные пути до полей связанных объектов
    - Расширенные условия, Оппозитные пути, Использование путей в условиях
    - Кеширование метаданных путей
    - Сокращение избыточного пути
 - События взаимосвязей `-> -> -> ->` `relation` `<- <- <- <-`.
 - Удаление объектов которые имеют файловую связку, с использованием программной каскадности, шаговое удаление в транзакции
 - Связанные файлы, Связанные директории, Менеджмент связанной файловой системы
 - Псевдонимы "полей ссылок" user.id === user_id(&)
 - Различные источники данных БД, Файловая система и прочее
 - Загрузка записей включая связанные 1-1 объекты
 - Загрузка связанной коллекции включая связи связанных записей 1-1
    - Можно рассматривать как ManyToMany
    - `load user.memberIn[usergroup] === Usergroup[]`

 - Создание записей и их связей
 - Поля кеширования(FormulaSet)
 
 
 
 String:camelize