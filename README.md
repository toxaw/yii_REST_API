yii migrate/create create_user_table --fields="login:string,password:string,token:string"

yii migrate/create create_post_table --fields="title:string:notNull,datetime:datetime,anons:string:notNull,text:text:notNull,rating:float,image:string:notNull,rating_sum:integer"

yii migrate/create create_comment_table --fields="post_id:integer:notNull:foreignKey(post),author:string:notNull,comment:text:notNull,datetime:datetime,rating:integer:notNull"
