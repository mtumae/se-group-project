import { Link } from '@tanstack/react-router'
import { CircleUser } from 'lucide-react';
import { useState } from 'react'

const menuleft = [
  {label:"StrathMart", route:"/"},
]

const menuright = [
  {route:"/profile", icon:<CircleUser size={28}/>},
]



export default function Header() {
  const [active, setActive]=useState("")
  console.log(active)
  return (
    <header className="p-4 w-full flex rounded-xl shadow-sm gap-2 bg-white text-black justify-between">
      <nav className='w-full'>
        <div className="px-2 flex  justify-between items-center">
          <div className='flex gap-10 items-center'>
          {menuleft.map((m)=>(
            <Link key={m.route} onClick={()=>setActive(m.route)} className={`${active==m.route?'text-green-400':'text-black'}`} to={m.route}>{m.label}</Link>
          ))}
          </div>
          <div className='flex items-center gap-5'>
            {menuright.map((m)=>(
               <Link key={m.route} to={m.route} onClick={()=>setActive(m.route)} className={`${active==m.route?"text-white bg-amber-500 shadow-md":"text-amber-500"} rounded-full   p-2 cursor-pointer hover:bg-amber-500 hover:text-white  transition-all duration-300`}>
                {m.icon}
               </Link>
            ))}
          </div>
        </div>
      </nav>
    </header>
  )
}
