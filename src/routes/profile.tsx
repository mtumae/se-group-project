import { createFileRoute, Link} from '@tanstack/react-router'
import { Button, Popover, Textarea } from "flowbite-react";
import { FileInput, Label, TextInput } from "flowbite-react";
import {  CirclePlus, CloudUpload, DollarSign,  EllipsisVertical, LayoutDashboard, Loader2, LogOut, Paperclip, X } from 'lucide-react';
import {  useState } from 'react';
import { useMutation } from 'convex/react';
import { useForm, type SubmitHandler} from "react-hook-form"
import { usePaginatedQuery } from "convex/react";
import { useQuery } from "@tanstack/react-query";
import { convexQuery } from '@convex-dev/react-query';

import { api } from "../../convex/_generated/api";
import { useAuthActions } from '@convex-dev/auth/react';
import { toast } from 'react-toastify';

interface ItemType {
  userId: string,
  username: string,
  imageUrl: string,
  itemName: string,
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
  const [newStatus, setNewStatus]=useState<'pending' | 'shipped' | 'delivered'|string>('');


  const {register, handleSubmit}= useForm<ItemType>();

  const {data:session} = useQuery(convexQuery(api.users.currentUser, {}));


  const { signOut } = useAuthActions();

  
  //Convex
  const generateUploadUrl = useMutation(api.items.generateUploadUrl);
  const updateStatus = useMutation(api.orders.updateOrderStatus)
  const addItem = useMutation(api.items.addItem);
  

 


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
      userId: session?._id!,
      username: session?.fullName!,
      imageUrl: fileobject.storageId,
      itemName: data.itemName,
      itemDescription: data.itemDescription,
      price: parseFloat(data.price),
      category: selectedCategory,
      createdAt: new Date().toDateString(),
    })
    toast.success(`${data.itemName} added successfully!`)
  }
    }catch(error){
      console.error("Error uploading file:", error);
      toast.error("Error uploading file. Please try again.");
    }finally{
      setLoading(false);
    }
    setLoading(false)
  };



  const {results} = usePaginatedQuery(api.orders.getOrders, {}, {initialNumItems:20})

  const {data:ordersForThisUser, isLoading:loadingOrdersForUser, refetch:refetchOrdersForUser} = useQuery(convexQuery(api.orders.getOrdersForUserId, {userId:session?._id!}))



  return(
    <div className='grid gap-10 '>
      <div className='flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4'>
        <h1>Welcome {session?.fullName}</h1>



  <div className='flex gap-5 flex-wrap'>
        {session?.role==="admin" && 
        <Link className='bg-gray-300 flex gap-4 border-gray-400 border rounded-md p-3 text-white' to="/admin">
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
   
  <div className='flex flex-col md:flex-row gap-5'>
  {file ? 
  <div className='relative w-full sm:w-auto'>
  <img src={fileSrc} alt="Uploaded Preview" className="mt-4 rounded-lg max-w-full sm:max-w-xs" />
  <div 
  onClick={()=>setFile(null)}
  className='flex p-2 hover:p-3 transition-all duration-300 text-white absolute right-3 top-3 bg-red-600 items-center cursor-pointer rounded'>
  <X className=''/>
  </div>
  </div>
      :
      <Label
        htmlFor="dropzone-file"
        className="flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-4 sm:p-6"
      >
        <div className="flex flex-col items-center justify-center pb-6 pt-5">
          <CloudUpload className="mb-3 h-10 w-10 text-gray-400" />
          <p className="mb-2 text-sm text-gray-500 dark:text-gray-400">
            <span className="font-semibold">Click to upload</span> or drag and drop
          </p>
          <p className="text-xs text-gray-500 dark:text-gray-400">PNG, JPG (MAX. 800x400px)</p>
        </div>
        <FileInput onChange={handleFileChange} id="dropzone-file" className="hidden" />
      </Label>
      }
      <div className='w-full'>
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className='flex flex-col sm:flex-row justify-between gap-3'>
              <TextInput color='white' required className='mb-4 w-full ' icon={Paperclip} placeholder='Item name' {...register('itemName')}/>
            </div>
            <div className='flex flex-col sm:flex-row gap-3'>
              
              <TextInput color='white' type="text" required className='mb-4 w-full ' icon={DollarSign} placeholder='Item price' {...register('price')}/>
            </div>
            <div className='mb-4'>
            <p className='mb-2'>Select a category</p>
            <div className='flex flex-wrap gap-2'>
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



      <div className=''>
      <h1>Orders you made</h1>
      <div className='grid grid-cols-1 gap-4 p-5 '>
        {results.map((o)=>(
          <div
          className='w-full text-xs flex flex-wrap gap-4 justify-between items-center' 
          key={o._id}>
            <p>{o.itemName}</p>
          
            <div className='flex'>
              
              {o.status=="pending" ? <p className='ml-2 text-yellow-500'>● pending</p> : o.status=="shipped" ? <p className='ml-2 text-blue-500'>● shipped</p> : <p className='ml-2 text-green-500'>● delivered</p>}
            
            </div>
            <p>{new Date(o._creationTime).toLocaleString()}</p>
          </div>
        ))}
      </div>
      </div>


      <div className=''>
        <h1>Orders you have gotten</h1>
         <div className='grid grid-cols-1 gap-4 p-5 '>
          {loadingOrdersForUser ? <p>Loading...</p> :
            ordersForThisUser?.map((o)=>(
              <div
              className='w-full text-xs flex flex-wrap gap-4 justify-between items-center' 
              key={o._id}>
                <p>{o.itemName}</p>
                  
                  {o.status=="pending" ? <p className='ml-2 text-yellow-500'>● pending</p> : o.status=="shipped" ? <p className='ml-2 text-blue-500'>● shipped</p> : <p className='ml-2 text-green-500'>● delivered</p>}
                  <p>{new Date(o._creationTime).toLocaleString()}</p>
                  <Popover 
                  trigger='click'
                  className='bottom-0 bg-white h-fit w-fit border text-black border-gray-200 focus:ring-0 focus:outline-0 rounded-md'
                  content={
                    <div className='grid gap-3 p-5'>
                      <select 
                        value={o.status}
                        onChange={(e) => {
                          setNewStatus(e.target.value)

                        }}
                      >
                        <option value={'pending'}>Pending</option>
                        <option value={'shipped'}>Shipped</option>
                        <option value={'delivered'}>Delivered</option>
                      </select>

                       <button 
                        className='bg-amber-500 text-white p-2 rounded-md mt-4 hover:bg-amber-600'
                      
                        onClick={()=>{
                            setLoading(true);
                            updateStatus({
                                orderId: o._id,
                                status:newStatus
                            }).then(()=>{
                                refetchOrdersForUser();
                                toast.success("Status updated successfully");
                                setLoading(false);
                            }).catch((err)=>{
                                console.error(err);
                                toast.error("Failed to update status");
                                setLoading(false);
                            })
                        }}>
                            {loading ? 'Updating...' : 'Update Status'}
                        </button>
                    </div>
                  }>
                    <EllipsisVertical size={20} className='cursor-pointer'/>
                  </Popover>
               
              </div>
            ))
          }
          
         </div>
      </div>
    </div>
  )
}
