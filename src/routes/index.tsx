import { createFileRoute } from '@tanstack/react-router'
import { useMutation, useQuery, useSuspenseQuery } from '@tanstack/react-query'
import { convexQuery, useConvexMutation  } from '@convex-dev/react-query'
import { useEffect } from 'react';
import { api } from "../../convex/_generated/api";
import { useState } from 'react';
import { Popover } from 'flowbite-react';
import { CirclePlus, ShoppingCart } from 'lucide-react';
import { Slider } from '@mui/material';

export const Route = createFileRoute('/')({
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
  const [search, setSearch]=useState("")
  const [categories, setCategories]=useState("")
  const [price, setPrice]=useState<number[]>([0,10000])
  const [error, setError]=useState('')
  const [ordering, setOrdering]=useState(false)

  const [queryData, setQueryData]=useState<any[]>([])

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
      setError('Failed to place order. Please try again!');
    }finally{
      setOrdering(false);
      setCart([])
    }
  }


  useEffect(()=>{
    if(search.length){
        refetchSearch()
    }
  }, [search])

  function handleSearchWPrice(){
    refetchPriceSearch()
    console.log("Price search triggered")
  }





  


  function incrementQuantity(itemId: string) {
    setCart(cart.map(item => item.itemId === itemId ? { ...item, quantity: item.quantity + 1 } : item));
  }


  const {data, isLoading, isError}= useQuery(convexQuery(api.items.getRandomItems, {}));
  const {data:searchData , refetch: refetchSearch}= useQuery(convexQuery(api.items.search, {query:search, category:categories}))
  const {data:priceSearchData , refetch: refetchPriceSearch}= useQuery(convexQuery(api.items.priceRangesearch, {maxPrice:price[1], minPrice:price[0]}))



  return (

  <div className='grid gap-5 items-center justify-center '> 

    <input 
      onChange={(e)=>{setSearch(e.target.value)}} 
      value={search} 
      placeholder='Search for an item'
      className=' p-3 rounded-lg border-gray-300 border '
      />

    <div className='flex justify-between items-center gap-5 p-10  rounded-lg'>
      {cart.length>0 && <div className='bg-amber-500 rounded-full absolute p-1' />}
      <Popover
      trigger='hover'
      aria-labelledby="default-popover"
      className='bottom-0 bg-white fixed h-1/2 w-auto border border-gray-200 focus:ring-0 focus:outline-0 rounded-md'
      content= {
        <div className='grid gap-5 p-10'>
          <h1 className='text-center'>Your Cart</h1>
          <div className=''>
            {cart.length>0?
            cart.map((order, index)=>(
              <div key={index} className='p-2 flex justify-between gap-5 items-center text-lg'>
                <p>{order.itemName}</p>
                <p>x{order.quantity}</p>
              
              </div>
            )):(
              <p className='text-gray-300 text-xs text-center'>Your cart is empty.</p>
            )}
          </div>
          {cart.length>0 &&
          <button
          onClick={checkOut} 
          className='p-2 mb-3 border text-amber-500 cursor-pointer rounded-full hover:bg-amber-500 focus:outline-0 focus:ring-0  hover:text-white  transition-all duration-300'>
            {ordering ? 'Checking out...' : 'Checkout'}
            </button>
            }
        </div>
      }
      >
        <button className='p-2 text-amber-500 cursor-pointer rounded-full hover:bg-amber-500 hover:text-white  transition-all duration-300'>
          <ShoppingCart size={30}/>
        </button>
      </Popover>
      <select onChange={(e)=>{setCategories(e.target.value)}} className='p-3 rounded-lg border-gray-300 border '>
        {Array.from(new Set(data?.map((item) => item.category))).map((cat) => (
          <option key={cat} value={cat}>{cat}</option>
        ))}
     
      </select>
      <Slider
      aria-label="Always visible"
      getAriaLabel={() => 'Price range'}
      value={price}
      onChange={(e, newValue) => {
          setPrice(newValue);

      }}
      valueLabelDisplay="on"
   
    />
    <button onClick={()=>{handleSearchWPrice}} type='submit' className='p-2 text-amber-500 w-96 cursor-pointer rounded-lg hover:bg-amber-500 hover:text-white  transition-all duration-300'>
      Search with filters
    </button>
    </div>
    {error && <p className='text-red-500'>{error}</p>}

    {priceSearchData?
    priceSearchData.map((item: any) => (
      <div className={`shadow-sm rounded-lg w-96  grid items-center`} key={item.name}>
        <img src={item.imageUrl ? item.imageUrl : ''} alt={item.name} width={200} className='w-full rounded-t-lg' />
      </div>
    )) : null}

    {isLoading ? <p>Loading...</p> : isError ? <p>Error loading items.</p> : (
        <div className='sm:grid md:flex w-full flex-wrap gap-8'>
        {data?
        data.map((item) => (
          <div className={`shadow-sm rounded-lg w-96  grid items-center`} key={item.name}>
            <img src={item.imageUrl ? item.imageUrl : ''} alt={item.name} width={200} className='w-full rounded-t-lg' />


          <div className='p-10'>
            <div className='flex justify-between items-center '>
              <div>
                <div>
                <h2 className=''>{item.name}</h2>
                <p className='text-sm text-gray-700'>{item.username}</p>

                </div>
              </div>

            
            <button
            className='p-2 text-amber-500 cursor-pointer rounded-lg  hover:bg-amber-500 hover:text-white   transition-all duration-300'
            onClick={()=>{
              addToCart({
                userId:'mtume1234',
                itemId:item.id,
                itemName:item.name,
                quantity:1,
              })
            }}
            >
              <CirclePlus />
            </button>
            </div>
              <p className='text-xs text-gray-600'>{item.description}</p>
              </div>
          </div>
        ))
        :<p>No items found.</p>
      }
   </div>
    )}
  </div>
  )
}
