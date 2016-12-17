<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.12.2016
 * Time: 17:22
 */
namespace Jungle\Util\Path {

	/**
	 * Class PathResolver
	 * @package Jungle\Util\Path
	 *
	 *   PATH:   user.id
	 *
	 * PATHS{from {user}}:
	 *
	 *   PATH:   {profile.first_name}
	 *
	 *   PATH:   {profile.getFio()}
	 *
	 *   PATH:   {profile.getFio('arg1','arg2')}
	 *
	 *   PATH:   {profile.getFio( &ref_arg1 , &ref_arg2 )}
	 *
	 *   PATH:   {memberIn[0].key}
	 *
	 *
	 * ~ Object resolving
	 * ~ Array assoc resolving
	 * ~ Array indexed resolving
	 * ~ #improve &reference resolving
	 * ~ Method resolving
	 *
	 */
	class PathResolver{
		
	}
}

