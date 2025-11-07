import { useQuery } from '@tanstack/react-query'
import { convexQuery } from '@convex-dev/react-query'
import { api } from "../../convex/_generated/api";
import { CircleUser } from 'lucide-react';
import { Skeleton } from '@mui/material';
    
export default function ordersDash(){
    const {data, isLoading, refetch}=useQuery(convexQuery(api.orders.getAllOrders, {}));

    return(
        <div className='mt-10'>
            <div className='flex justify-between items-center'>
                <h1>Item</h1>
                <h1>Quantity</h1>
                <h1>Date ordered</h1>
                <h1>Status</h1>
            </div>
            <div>
                {isLoading && 
                <div>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                </div>
                }

                {data?
                    data.map((order) => (
                        <div key={order._id} className='border-b border-gray-200 py-4 flex justify-between items-center'>
                            <h2 className='text-lg font-semibold'>{order.itemName}</h2>
                            <p>{order.userId}</p>
                            <p className='text-sm text-gray-500'>x{order.quantity}</p>
                            <p>{new Date(order.orderDate).toLocaleDateString()}</p>
                            {order.status=="pending" ? <p className='ml-2 text-yellow-500'>● pending</p> : order.status=="shipped" ? <p className='ml-2 text-blue-500'>● shipped</p> : <p className='ml-2 text-green-500'>● delivered</p>}
                        </div>
                    ))
                : (
                    <p>No orders found</p>
                )}
            </div>
        </div>
    )
}