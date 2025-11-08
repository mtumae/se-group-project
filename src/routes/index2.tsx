import { createFileRoute } from '@tanstack/react-router'

export const Route = createFileRoute('/index2')({
  component: App,
})




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
