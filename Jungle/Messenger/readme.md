# Messenger
Структура рассылки сообщений в которой можно реализовать разные адаптеры

# Основа:
	Combination( Message , Contact[] )
	Messenger::send( Combination );
	
# Contact
	
объект Contact представляет из себя адррес пользователя в первую очередь и может быть 
расширен включая в себя другие свойства представляющие контакт-пользователя (Name, Gender, Years)

# Message
объект Message представляет из себя содержание сообщения, поддерживает расширение

# Combination
Комбинация Message and Contact[] , которая собирает в себе рассылку, где Messager сам определит как разослать сообщение всем контактам