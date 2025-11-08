import { createFileRoute } from '@tanstack/react-router'
import { useMutation, useQuery, useSuspenseQuery } from '@tanstack/react-query'
import { convexQuery, useConvexMutation  } from '@convex-dev/react-query'
import { useEffect } from 'react';
import { api } from "../../convex/_generated/api";
import { useState } from 'react';
import { Popover } from 'flowbite-react';
import { CircleMinus, CirclePlus, ShoppingCart } from 'lucide-react';
import { Slider } from '@mui/material';
import { toast } from 'react-toastify';

export const Route = createFileRoute('/')({
  component: RouteComponent,
})


// interface OrderType {
//   userId: string,
//   itemId: string,
//   itemName:string,
// }




interface ItemType {
  userId: string,
      username: string,
      imageUrl: string | null,
      link: string | null,
      itemName: string,
      itemDescription: string,
      price: number,
      category: string,
      createdAt: string,
      disabled: boolean | null,

}
function RouteComponent() {
  const [cart, setCart]=useState<any[]>([])
  const [search, setSearch]=useState("")

  const [category, setCategory]=useState("")
  const [searchingcategory, setSearchingCategory]=useState(false)


  const [price, setPrice]=useState<number[]>([0,10000])
  const [error, setError]=useState('')
  const [ordering, setOrdering]=useState(false)

  

  const [queryData, setQueryData]=useState<any[]>([])

  const order = useConvexMutation(api.orders.addOrder);
  


  function addToCart(item:any) {
    setCart([...cart, item]);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  async function checkOut(){
    setOrdering(true);
    try{
      await order({
      items: cart.map((i)=>({
        userId:i.userId,
        username:i.username,
        itemId:i.itemId,
        itemName:i.itemName,
      }))
    })
    toast.success("Order placed successfully!")

    }catch(error){
      console.error("Error placing order:", error);
      toast.error("Error placing order. Please try again.");
    }finally{
      setOrdering(false);
      setCart([])
    }
  }

  const {data:session, isLoading:loadingSession} = useQuery(convexQuery(api.users.currentUser, {}));
  
  





  const {data, isLoading, isError}= useQuery(convexQuery(api.items.getAllItems, {}));


  const searchQuery = useQuery( convexQuery(api.items.search, {query:search, category:category}));


//   const {data:priceSearchData , refetch: refetchPriceSearch}= useQuery({
//     queryFn:() => convexQuery(api.items.priceRangesearch, {maxPrice:price[1], minPrice:price[0]}),
//     enabled: !!price,
// });


  return (

  <div className='grid gap-5 items-center justify-center w-full px-4'> 

  <div className='flex gap-4 justify-between'>
    <select onChange={(e)=>{
        setCategory(e.target.value)
        setSearchingCategory(true)
        console.log("searching category:", category)
        }} value={category} className='p-3 rounded-lg border-gray-300 border w-full sm:w-auto'>
        {Array.from(new Set(data?.map((item) => item.category))).map((cat) => (
          <option key={cat} value={cat}>{cat}</option>
        ))}
      </select>

       <input 
      onChange={(e)=>{setSearch(e.target.value)}} 
      value={search} 
      placeholder='Search for an item'
      className='p-3 rounded-lg border-gray-300 border w-full max-w-xl'
      />

       <Popover
      trigger='hover'
      aria-labelledby="default-popover"
      className='relative bg-white rounded-md border-gray-300 border'
      content= {
        <div className='grid gap-4 p-4 sm:p-6 w-72 sm:w-96'>
          <h1 className='text-center'>Cart Total {cart.map((i)=>i.price).reduce((a,b)=>a+b,0)}ksh</h1>
          <div className=''>
            {cart.length>0?
            cart.map((order, index)=>(
              <div key={index} className='p-2 flex justify-between gap-5 items-center text-sm'>
                <p>{order.itemName}</p>
                <p>{order.price}ksh</p>
            
             

                <button 
                onClick={()=>
                {

                  setCart(cart.filter((i)=> i.itemId !== order.itemId));

                }} className='text-red-500'>
                  <CircleMinus />
                </button>
              </div>
            )):(
              <p className='text-gray-300 text-xs text-center'>Your cart is empty.</p>
            )}
          </div>
      
          {cart.length>0 &&

          <button
          onClick={checkOut} 
          className='p-2 mb-3 border text-amber-500 cursor-pointer rounded-full  hover:bg-amber-500 focus:outline-0 focus:ring-0  hover:text-white  transition-all duration-300'>
            {ordering ? 'Checking out...' : 'Checkout'}
            
            </button>
            }
        </div>
      }
      >
        <button className='p-2 text-amber-500 cursor-pointer rounded-full hover:bg-amber-500 hover:text-white  transition-all duration-300'>
          <ShoppingCart className={cart.length>0?"animate-bounce":''} size={36}/>
        </button>
      </Popover>
    

  </div>
   

  

    <div className='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 w-full gap-8'>
      {isLoading ? <p>Loading...</p>
      : data?
      data.map((item) => (
        <div className={`shadow-sm rounded-lg w-full sm:w-96 mb-10 grid items-center`} key={item._id}>
          {item.url &&<img src={item.url} alt={item.itemName} width={200} className='w-full rounded-t-lg' />}
          {item.link && <img src={item.link} alt={item.itemName} width={200} className='w-full rounded-t-lg' />}
          <div className='p-10'>
            <div className='flex justify-between items-center '>
              <div>
                <div>
                  <h2 className=''>{item.itemName} - {item.price}ksh</h2>
                  <p className='text-sm text-gray-700'>{item.username}</p>

                </div>
              </div>

             
            <button
            disabled={cart.find((i)=>i.itemId===item._id)?true:false}
            className='p-2 text-amber-500 cursor-pointer rounded-lg  hover:bg-amber-500 hover:text-white transition-all duration-300'
            onClick={()=>{
              if(item.userId===session?._id){
                toast.error("You cannot add your own item to the cart.")
                return;
              }
              addToCart({
                userId:session?._id,
                username:session?.fullName,
                itemId:item._id,
                itemName:item.itemName,
                price:item.price, 
              })
            }}
            >
              <CirclePlus />
            </button>

            </div>
              <p className='text-xs text-gray-600'>{item.itemDescription}</p>
              </div>
          </div>
        ))
        :<p>No items found.</p>
      }
   </div>

   
  </div>
  )
}
