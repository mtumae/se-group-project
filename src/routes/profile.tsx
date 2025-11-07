import { createFileRoute, Link, useNavigate } from '@tanstack/react-router'
import { Button, Select, Textarea } from "flowbite-react";
import { FileInput, Label, TextInput } from "flowbite-react";
import { Calculator, CirclePlus, CloudUpload, DollarSign, LayoutDashboard, Loader2, LogOut, Paperclip, Plus, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { useMutation } from 'convex/react';
import { useForm, type SubmitHandler} from "react-hook-form"
import { usePaginatedQuery } from "convex/react";
import { useSuspenseQuery, useQuery } from "@tanstack/react-query";
import { convexQuery } from '@convex-dev/react-query';

import { api } from "../../convex/_generated/api";
import { useAuthActions } from '@convex-dev/auth/react';

interface ItemType {
  userId: string,
  imageUrl: string,
  itemName: string,
  quantity: string,
  itemDescription: string,
  price: string,
  category: string,
  createdAt: string,

}



export const Route = createFileRoute('/profile')({
  component: RouteComponent,
})

const categories = [
  {value:"electronics", label:"Electronics"},
  {value:"clothing", label:"Clothing"},
  {value:"books", label:"Books"},
  {value:"sports", label:"Sports"},
  {value:"stationery", label:"Stationery"},
  {value:"others", label:"Others"},
]




function RouteComponent() {
  const [selectedCategory, setSelectedCategory]=useState("")
  const [loading, setLoading]=useState(false)
  const [file, setFile]=useState<File|null>(null)
  const [fileSrc, setFileSrc]=useState("")

  const {register, handleSubmit, watch, formState: {errors}}= useForm<ItemType>();

  const {data:session, isLoading:loadingSession} = useQuery(convexQuery(api.users.currentUser, {}));


  const { signOut } = useAuthActions();

  
  //Convex
  const generateUploadUrl = useMutation(api.items.generateUploadUrl);
  const addItem = useMutation(api.items.addItem);


  //Router
  const navigate = useNavigate();



 


  function handleFileChange(event: React.ChangeEvent<HTMLInputElement>) {
    const selectedFile = event.target.files?.[0] || null;
    setFile(selectedFile);
    setFileSrc(URL.createObjectURL(selectedFile!));
  }


  const onSubmit: SubmitHandler<ItemType> = async (data) => {
    setLoading(true);
    console.log(data)
     try{
    const fileURL = await generateUploadUrl();
    if(file){
    const res = await fetch(fileURL,{
      method:'POST',
      headers:{ "Content-Type": file.type},
      body:file
    })
    const fileobject = await res.json()
    await addItem({
      userId: "test-user",
      imageUrl: fileobject.storageId,
      itemName: data.itemName,
      quantity: data.quantity,
      itemDescription: data.itemDescription,
      price: data.price.toString(),
      category: selectedCategory,
      createdAt: new Date().toDateString(),
    })
  }
    }catch(error){
      console.error("Error uploading file:", error);
    }finally{
      setLoading(false);
    }
    setLoading(false)
  };



  const {results, status, loadMore} = usePaginatedQuery(api.orders.getOrders, {}, {initialNumItems:20})



  return(
    <div className='grid gap-10 justify-self-center'>
      <div className='flex justify-between'>
        <h1>Welcome {session?.fullName}</h1>



        <div className='flex gap-5'>
        {session?.role==="admin" && 
        <Link className='bg-gray-300 flex gap-4 border-gray-400 border rounded-md p-3 text-gray-400' to="/admin">
        Admin
        <LayoutDashboard />
        
        </Link>}

        <button
        className='bg-red-600 rounded-md flex gap-4 p-3 text-white' 
        onClick={
          ()=>{
            signOut()
          }
        }>
          Log out
          <LogOut />
          </button>
        </div>

      </div>
   
      <div className='md:flex sm:grid justify-between gap-5  '>
      {file ? 
      <div className='relative'>
      <img src={fileSrc} alt="Uploaded Preview" className="mt-4 rounded-lg max-w-xs" />
      <div 
      onClick={()=>setFile(null)}
      className='flex p-2 hover:p-3 transition-all duration-300 text-white absolute self-end top-3 bg-red-600 item-center cursor-pointer'>
      <X className=''/>
      </div>
      </div>
      :
      <Label
        htmlFor="dropzone-file"
        className="flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 "
      >
        <div className="flex flex-col items-center justify-center pb-6 pt-5">
          <CloudUpload className="mb-3 h-10 w-10 text-gray-400" />
          <p className="mb-2 text-sm text-gray-500 dark:text-gray-400">
            <span className="font-semibold">Click to upload</span> or drag and drop
          </p>
          <p className="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
        </div>
        <FileInput onChange={handleFileChange} id="dropzone-file" className="hidden" />
      </Label>
      }
      <div className='w-full'>
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className='flex justify-between gap-3'>
              <TextInput color='white' required className='mb-4 w-full ' icon={Paperclip} placeholder='Item name' {...register('itemName')}/>
             
            </div>
            <div className='flex justify-between gap-3'>
              <TextInput color='white' required className='mb-4 w-full  ' icon={Calculator} placeholder='Item quantity' {...register('quantity')}/>
              <TextInput color='white' type="number" required className='mb-4 w-full ' icon={DollarSign} placeholder='Item price' {...register('price')}/>
            </div>
            <div className='mb-4'>
            <p className='mb-2'>Select a category</p>
            <div className='flex gap-2'>
              {categories.map((c)=>(
                <div {...register('category')} onClick={()=>setSelectedCategory(c.value)} key={c.value} className={`p-2  border ${selectedCategory==c.value?'border-green-400 bg-green-100 shadow-md':'border-gray-300'} rounded-md cursor-pointer hover:border-green-400 hover:bg-green-100 hover:shadow-md transition-all duration-300`}>
                  {c.label}
                </div>
              ))}
            </div>
            </div>
            <Textarea color='white' id="comment" className='mb-3 border-gray-400 ' placeholder="Item description" required rows={2} {...register('itemDescription')} />
            <Button type='submit' color="green" className='w-full  cursor-pointer flex items-center gap-1'>{ loading? <Loader2 className='animate-spin' />:<>Add Item <CirclePlus size={18} /></>}</Button>
          </form>
          </div>
      </div>


<div className='flex justify-between'>

    <div className=''>
      <h1>Orders you made</h1>
      <div className='grid grid-cols-2 p-5'>
        {results.map((o)=>(
          <div
          className='w-full text-xs flex gap-8 justify-between' 
          key={o._id}>
            <p>{o.itemName}</p>
            <p>x{o.quantity}</p>
            <div className='flex'>
              
              {o.status=="pending" ? <p className='ml-2 text-yellow-500'>● pending</p> : o.status=="shipped" ? <p className='ml-2 text-blue-500'>●</p> : <p className='ml-2 text-green-500'>●</p>}
            
            </div>
            <p>{new Date(o._creationTime).toLocaleString()}</p>
          </div>
        ))}
      </div>
      </div>


      <div className=''>
        <h1>Orders you have gotten</h1>
      </div>

      </div>



    </div>
  )
}
