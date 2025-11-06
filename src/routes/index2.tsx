import { createFileRoute } from '@tanstack/react-router'
import logo from '../logo.svg'

export const Route = createFileRoute('/index2')({
  component: App,
})


const promos = [
  {title:"50% off on all electronics!", description:"Get the best deals on electronics this week only."},
  {title:"Buy 1 Get 1 Free on Clothing!", description:"Refresh your wardrobe with our exclusive clothing offer."},
  {title:"Free Shipping on Orders Over $50!", description:"Enjoy free shipping when you spend $50 or more."},
]

function App() {

  return (
    <div className='grid gap-10'>
      

      <div className=' rounded-lg '>

        <img src='elecpromo2.jpg' sizes='100'  className='rounded-lg w-80'/>

        <div className='bg-white -mt-1text-center p-5'>
          <p>50% off on all electronics!</p>
          <p className='text-sm'>Get the best deals on electronics this week only.</p>
        </div>


      </div>




    </div>
  )
}
