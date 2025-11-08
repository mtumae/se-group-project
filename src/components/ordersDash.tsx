import { useQuery } from '@tanstack/react-query'
import { convexQuery } from '@convex-dev/react-query'
import { api } from "../../convex/_generated/api";
import { CircleUser, EllipsisVertical } from 'lucide-react';
import { Select, Skeleton, MenuItem, InputLabel } from '@mui/material';
import { useMutation } from 'convex/react';
import { Popover } from 'flowbite-react';
import { useState } from 'react';
import { toast } from 'react-toastify';

    
export default function ordersDash(){
    const [loading, setLoading]=useState(false);
    const [newstatus, setNewStatus]=useState<'pending' | 'shipped' | 'delivered'|string>('');
    const {data, isLoading, refetch}=useQuery(convexQuery(api.orders.getAllOrders, {}));
    const updateStatus=useMutation(api.orders.updateOrderStatus);
    const deleteOrder=useMutation(api.orders.deleteOrder);
   



    return(
        <div className='mt-10'>
            <div className='flex justify-between items-center'>
               
            </div>
            <div>
                {isLoading && 
                <p>Loading...</p>
                }

                {data?
                    data.map((order) => (
                        <div key={order._id} className='border-b border-gray-200 py-4 flex justify-between items-center'>
                            <h2 className='text-lg font-semibold '>{order.itemName}</h2>
                            <p className=''>{order.userId}</p>
                  
                            <p className=''>{new Date(order.orderDate).toLocaleDateString()}</p>
                            {order.status=="pending" ? <p className='ml-2 text-yellow-500'>● pending</p> : order.status=="shipped" ? <p className='ml-2 text-blue-500'>● shipped</p> : <p className='ml-2 text-green-500'>● delivered</p>}

                             <Popover
                                trigger='click'
                                aria-labelledby="default-popover"
                                className='bottom-0 bg-white h-fit w-fit border text-black border-gray-200 focus:ring-0 focus:outline-0 rounded-md'
                                content= {
                                    <div className='grid gap-3 p-5'>
                                       <button onClick={() => deleteOrder({orderId: order._id})} 
                                       className='p-2 hover:bg-gray-100 w-full text-left rounded-md text-red-500'>
                                        Delete Order</button>
                                   
                                         <select
                                            value={order.status}
                                            onChange={(e) => setNewStatus(e.target.value)}
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
                                                orderId: order._id,
                                                status: newstatus
                                            }).then(()=>{
                                                refetch();
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
                                }
                                >
                                    <button>
                                        <EllipsisVertical size={20} className=''/>
                                    </button>
                                </Popover>
                        </div>
                    ))
                : (
                    <p>No orders found</p>
                )}
            </div>
        </div>
    )
}