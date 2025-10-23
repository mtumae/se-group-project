import { createFileRoute } from '@tanstack/react-router'
import { useMutation, useQuery, useSuspenseQuery } from '@tanstack/react-query'
import { convexQuery, useConvexMutation  } from '@convex-dev/react-query'

import { api } from "../../convex/_generated/api";
import { useState } from 'react';
import { Popover } from 'flowbite-react';
import { ShoppingCart } from 'lucide-react';

 

export const Route = createFileRoute('/market')({
  component: RouteComponent,
})


interface OrderType {
  userId: string,
  itemId: string,
  itemName:string,
  quantity: number,
}

function RouteComponent() {
  const [cart, setCart]=useState<OrderType[]>([])
  const [ordering, setOrdering]=useState(false)
  const order = useConvexMutation(api.orders.addOrder);


  function addToCart(item: OrderType) {
    if (cart.find(cartItem => cartItem.itemId === item.itemId)) {
      incrementQuantity(item.itemId);
      return;
    }
    setCart([...cart, item]);
  }

  async function checkOut(){
    setOrdering(true);
    try{
      await order({
      items: cart
    })

    }catch(error){
      console.error("Error placing order:", error);
    }finally{
      setOrdering(false);
      setCart([])
    }
  }

  


  function incrementQuantity(itemId: string) {
    setCart(cart.map(item => item.itemId === itemId ? { ...item, quantity: item.quantity + 1 } : item));
  }


  const {data, isLoading, isError}= useQuery(convexQuery(api.items.getItems, {}));




  return (

  <div className='grid gap-5'> 

    <div>
      {cart.length>0 && <div className='bg-amber-500 rounded-full absolute p-1' />}
      <Popover
      trigger='hover'
      aria-labelledby="default-popover"
      className='bottom-0 bg-white fixed w-auto border border-gray-200 focus:ring-0 focus:outline-0 rounded-md'
      content= {
        <div className='grid gap-5 p-10'>
          <h1 className='text-center'>Your Cart</h1>
          <div className=''>
            {cart.length>0?
            cart.map((order, index)=>(
              <div key={index} className='p-2 flex justify-between gap-5 items-center text-lg'>
                <p>Item: {order.itemName}</p>
                <p>Quantity: {order.quantity}</p>
              
              </div>
            )):(
              <p className='text-gray-300 text-xs text-center'>Your cart is empty.</p>
            )}
          </div>
          <button
          onClick={checkOut} 
          className='p-2 mb-3 border text-amber-500 cursor-pointer rounded-full hover:bg-amber-500 focus:outline-0 focus:ring-0  hover:text-white  transition-all duration-300'>
            {ordering ? 'Checking out...' : 'Checkout'}
            </button>
        </div>
      }
      >
        <button className='p-2 text-amber-500 cursor-pointer rounded-full hover:bg-amber-500 hover:text-white  transition-all duration-300'>
          <ShoppingCart size={18}/>
        </button>
      </Popover>

    </div>
    {isLoading ? <p>Loading...</p> : isError ? <p>Error loading items.</p> : (
        <div className='flex gap-8'>
        {data?
        data.map((item) => (
          <div className='shadow-sm rounded-lg p-10 text-center' key={item._id}>
            <h2>{item.itemName}</h2>
            <p>{item.itemDescription}</p>
            <p>Price: ${item.price}</p>
            <img src={item.url ? item.url : ''} alt={item.itemName} width={200} />
            <button
            className='p-2 text-amber-500 cursor-pointer rounded-lg hover:bg-amber-500 hover:text-white  transition-all duration-300'
            onClick={()=>{
              addToCart({
                userId:'mtume1234',
                itemId:item._id,
                itemName:item.itemName,
                quantity:1,
              })
            }}
            >Add {item.itemName} to Cart</button>
          </div>
        ))
        :<p>No items found.</p>
      }
   </div>
    )}
  </div>
  )
}
