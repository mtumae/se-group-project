import { Link } from '@tanstack/react-router'
import { useState } from 'react'


const menus = [
  {label:"Home", route:"/"},
  {label:"Market", route:"/market"},
  {label:"Profile", route:"/profile"},
]

export default function Header() {
  const [active, setActive]=useState("/")
  return (
    <header className="p-2 flex gap-2 bg-white text-black justify-between">
      <nav className="flex flex-row">
        <div className="px-2  flex gap-5 items-center">
          {menus.map((m)=>(
            <Link onClick={()=>setActive(m.route)} className={`${active==m.route?'text-green-400':'text-black'}`} to={m.route}>{m.label}</Link>
          ))}
        </div>
      </nav>
    </header>
  )
}
