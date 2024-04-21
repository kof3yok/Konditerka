// Код, отвечающий за вкладки
package com.example.cakeapp.placeholder

import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.fragment.app.FragmentPagerAdapter
import androidx.fragment.app.FragmentStatePagerAdapter
// Класс ViewPagerAdapter: Этот класс наследуется от FragmentStatePagerAdapter, который является подклассом PagerAdapter и предназначен для работы с фрагментами в ViewPager.
// Конструктор: Конструктор класса принимает объект FragmentManager, который используется для управления фрагментами.
class ViewPagerAdapter(manager: FragmentManager) : FragmentStatePagerAdapter(manager, BEHAVIOR_RESUME_ONLY_CURRENT_FRAGMENT) {

    private val fragmentList = mutableListOf<Fragment>()
    private val titleList = mutableListOf<String>()
// Переопределение метода getCount(): Этот метод возвращает общее количество фрагментов, которые будут отображаться в ViewPager.
    override fun getCount(): Int {
        return fragmentList.size
    }
// Переопределение метода getItem(position: Int): Этот метод возвращает фрагмент по указанной позиции в ViewPager.
    override fun getItem(position: Int): Fragment {
        return fragmentList[position]
    }
// Переопределение метода getPageTitle(position: Int): Этот метод возвращает заголовок для вкладки в TabLayout, связанной с фрагментом по указанной позиции.
    override fun getPageTitle(position: Int): CharSequence? {
        return titleList[position]
    }
// Метод addFragment(fragment: Fragment, title: String): Этот метод добавляет новый фрагмент и его заголовок в списки fragmentList и titleList соответственно. Это позволяет динамически добавлять фрагменты в адаптер.
    fun addFragment(fragment: Fragment, title: String) {
        fragmentList.add(fragment)
        titleList.add(title)
    }
}
