import { createFileRoute } from '@tanstack/react-router'
import { useState } from 'react';
import UsersDash from '@/components/usersDash';
import OrdersDash from '@/components/ordersDash';
import ItemsDash from '@/components/itemsDash';




export const Route = createFileRoute('/admin')({
  component: RouteComponent,
})



function RouteComponent() {
    const [dashType, setDashType]=useState<'users' | 'orders' | 'items'>("users")
   
    function handleDashChange() {
        switch(dashType) {
            case 'users':
                return <UsersDash />;
            case 'orders':
                return <OrdersDash />;
            case 'items':
                return <ItemsDash />;
        }
    }


  return (
    <div className='mt-10'>

        <div className='justify-self-center flex gap-10'>
            <button onClick={()=>{setDashType('users')}} className={`${dashType === 'users' ? 'bg-amber-500 text-white' : ''} text-xl mb-4  text-amber-500 p-2 rounded-md transition-all duration-200`}>Users</button>
            <button onClick={()=>{setDashType('orders')}} className={`${dashType === 'orders' ? 'bg-amber-500 text-white' : ''} text-xl mb-4 text-amber-500 p-2 rounded-md transition-all duration-200`}>Orders</button>
            <button onClick={()=>{setDashType('items')}} className={`${dashType === 'items' ? 'bg-amber-500 text-white' : ''} text-xl mb-4 text-amber-500 p-2 rounded-md transition-all duration-200`}>Items</button>
        </div>


        {handleDashChange()}
    
    </div>
  )
}
